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

/**
 * Provides the routes for all footer links
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class AboutController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ){}

    #[Route(path: "/a-propos", name: "about_index")]
    public function index(): Response
    {
        return $this->render('about/index.html.twig');
    }

    #[Route(path: "/conditions", name: "about_cgu")]
    public function conditions(): Response
    {
        return $this->render('about/cgu.html.twig');
    }

    #[Route(path: "/confidentialite", name: "about_confidentialite")]
    public function confidentialite(): Response
    {
        return $this->render('about/confidentialite.html.twig');
    }

    #[Route(path: "/signaler", name: "report")]
    public function report(Request $request, MailerInterface $mailer): Response
    {
        $report  = new Report();
        $form = $this->createForm(ReportForm::class, $report);
        $form->handleRequest($request);
        $user = $this->getUser();

        if($form->isSubmitted() && $form->isValid()) {
            $report->setUser(null);
            if($user instanceof User) {
                $report->setUser($user);
            }
            try {
                $report
                    ->setCreatedAt(new DateTime('now', new DateTimeZone("Europe/Paris")))
                    ->setUpdatedAt(new DateTime('now', new DateTimeZone("Europe/Paris")));
            } catch (Exception) {
                return new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
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