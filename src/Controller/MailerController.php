<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Error\LoaderError;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;

class MailerController extends AbstractController
{
    /**
     * @param MailerInterface $mailer
     * @param string $to
     * @param string $subject
     * @param array $options
     * @param string $template
     * @throws LoaderError
     */
    public function send(MailerInterface $mailer, string $to, string $subject, array $options, string $template)
    {
        for ($i=0; $i < 6; $i++) {
            if(!isset($options[$i])) {
                $options[$i] = null;
            }
        }
        $loader = new FilesystemLoader(__DIR__.'/../../templates/');
        $loader->addPath(__DIR__.'/../../public/images', 'images');
        $loader->addPath(__DIR__.'/../../public/build', 'css');
        $twig = new TwigEnvironment($loader);
        $twig->addExtension(new CssInlinerExtension());
        $twigBodyRenderer = new BodyRenderer($twig);

        $email = (new TemplatedEmail())
            ->from(new Address("ne-pas-repondre@clickntoulon.fr", "ClickNToulon"))
            ->to(new Address($to))
            ->replyTo(new Address('administration@clickntoulon.fr', "ClickNToulon"))
            ->subject($subject)
            ->htmlTemplate('emails/' . $template . '.html.twig')
            ->context([
                "name" => $options[0],
                "id" => $options[1],
                "date" => $options[2],
                "time_begin" => $options[3],
                "time_end" => $options[4],
                "message" => $options[5]
            ])
        ;
        try {
            $twigBodyRenderer->render($email);
            $mailer->send($email, NULL);
        } catch (TransportExceptionInterface $e) {
            $e->getDebug();
        }
    }
}
