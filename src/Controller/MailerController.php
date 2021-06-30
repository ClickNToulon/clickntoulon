<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Error\LoaderError;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Loader\FilesystemLoader;

class MailerController extends AbstractController
{
    /**
     * @param MailerInterface $mailer
     * @param $to
     * @param $subject
     * @param $options
     * @param $template
     * @throws LoaderError
     */
    public function send(MailerInterface $mailer, $to, $subject, $options, $template)
    {
        for ($i=0; $i < 6; $i++) {
            if(!isset($options[$i])) {
                $options[$i] = null;
            }
        }
        $loader = new FilesystemLoader('..\templates\\');
        $loader->addPath('../public/images', 'images');
        $loader->addPath('../public/css', 'css');
        $twig = new TwigEnvironment($loader);
        $twig->addExtension(new CssInlinerExtension());

        $twigBodyRenderer = new BodyRenderer($twig);

        $email = (new TemplatedEmail())
            ->from(new Address("donotreply.tousolidaires@gmail.com", "TouSolidaires"))
            ->to(new Address($to))
            ->replyTo(new Address('tousolidaires@yncrea.fr', "TouSolidaires"))
            ->subject($subject)
            ->htmlTemplate('emails/' . $template . '.html.twig')
            ->context([
                "name" => $options[0],
                "id" => $options[1],
                "date" => $options[2],
                "time_begin" => $options[3],
                "time_end" => $options[4],
                "message" => $options[5]
            ]);

        try {
            $twigBodyRenderer->render($email);
            $mailer->send($email, NULL);
        } catch (TransportExceptionInterface $e) {
            $e->getDebug();
        }
    }
}
