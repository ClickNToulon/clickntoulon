<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Report\Report;
use App\Http\Form\ReportForm;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Provides the routes for all footer links
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class AboutController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ){}

    #[Route(path: "/a-propos", name: "about_index")]
    public function index(): Response
    {
        return $this->render('about/index.html.twig', [
            "menu" => "about"
        ]);
    }

    #[Route(path: "/conditions", name: "about_cgu")]
    public function conditions(): Response
    {
        return $this->render('about/cgu.html.twig', [
            "menu" => "cgu"
        ]);
    }

    #[Route(path: "/confidentialite", name: "about_confidentialite")]
    public function confidentialite(): Response
    {
        return $this->render('about/confidentialite.html.twig', [
            "menu" => "confidential"
        ]);
    }

    #[Route(path: "/signaler", name: "report")]
    public function report(Request $request, MailerInterface $mailer): Response
    {
        $report  = new Report();
        $form = $this->createForm(ReportForm::class, $report);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $report->setUser(null);
            $user = $this->getUser();
            if($user instanceof User) {
                $report->setUser($user);
            }
            $email = $request->request->all('report_form')['email'];
            try {
                $report
                    ->setEmail($email)
                    ->setCreatedAt(new DateTime('now', new DateTimeZone("Europe/Paris")))
                    ->setUpdatedAt(new DateTime('now', new DateTimeZone("Europe/Paris")));
            } catch (Exception) {
                $this->addFlash('warning', "Un problème technique est survenu et nous n'avons pas pu prendre en compte votre demande. Veuillez réessayer plus tard.");
            }
            $this->em->persist($report);
            $this->em->flush();
            $this->addFlash('success', 'Votre signalement a bien été pris en compte. Il sera traité dans les plus brefs délais.');
            $name = "Anonyme";
            if($report->getUser() !== null) {
                $name = $report->getUser()->getName() . " " . $report->getUser()->getSurname();
            }
            $title = "Un signalement a été effectué sur ClickNToulon par " . $name;
            $options = [];
            $options[] = $name;
            (new MailerController)->send($mailer, "administration@clickntoulon.fr", $title, $options, 'reportcontent');
        }
        return $this->render('about/report.html.twig', [
            'form' => $form->createView(),
            "menu" => "report"
        ]);
    }
}