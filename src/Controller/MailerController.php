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
use Twig\Loader\FilesystemLoader;

/**
 * Provides the Symfony Mailer integration with Twig
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class MailerController extends AbstractController
{
    public function send(MailerInterface $mailer, string $to, string $subject, array $options, string $template)
    {
        for ($i=0; $i < 4; $i++) {
            if(!isset($options[$i])) {
                $options[$i] = null;
            }
        }
        $loader = new FilesystemLoader(__DIR__.'/../../templates/');
        try {
            $loader->addPath(__DIR__.'/../../public/images', 'images');
            $loader->addPath(__DIR__.'/../../public/build', 'css');
        } catch (LoaderError) {
            return new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
                "message" => $options[3]
            ]);
        try {
            $twigBodyRenderer->render($email);
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $e->getDebug();
        }
    }
}
