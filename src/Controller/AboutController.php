<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\User;
use App\Form\ReportForm;
use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;

class AboutController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ){}

    /**
     * @return Response
     */
    #[Route(path: "/a-propos", name: "about_index")]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('about/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @return Response
     */
    #[Route(path: "/conditions", name: "about_cgu")]
    public function conditions(): Response
    {
        $user = $this->getUser();
        return $this->render('about/cgu.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @return Response
     */
    #[Route(path: "/confidentialite", name: "about_confidentialite")]
    public function confidentialite(): Response
    {
        $user = $this->getUser();
        return $this->render('about/confidentialite.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws LoaderError
     */
    #[Route(path: "/signaler", name: "report")]
    public function report(Request $request, MailerInterface $mailer): Response
    {
        $report  = new Report();
        $form = $this->createForm(ReportForm::class, $report);
        $form->handleRequest($request);
        $user = $this->getUser();

        if($form->isSubmitted() && $form->isValid()) {
            if($user instanceof User) {
                $report->setUser($user);
            } else {
                $report->setUser(null);
            }
            $report->setCreatedAt(new DateTime('now', new DateTimeZone("Europe/Paris")));
            $report->setUpdatedAt(new DateTime('now', new DateTimeZone("Europe/Paris")));
            $this->em->persist($report);
            $this->em->flush();
            $this->addFlash('success', 'Votre signalement a bien été pris en compte. Il sera traité dans les plus brefs délais');
            $name = $report->getUser()->getName() . " " . $report->getUser()->getSurname();
            $title = "Un signalement a été effectué sur ClickNToulon par " . $name;
            $options = [];
            $options[] = $name;
            (new MailerController)->send($mailer, "administration@clickntoulon.fr", $title, $options, 'reportcontent');
        }

        if($user instanceof User) {
            return $this->render('about/report.html.twig', [
                'user' => $user,
                'form' => $form->createView()
            ]);
        }
        return $this->render('about/report.html.twig', [
            'form' => $form->createView()
        ]);
    }
}