<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Http\Form\User\ChangePasswordForm;
use App\Http\Form\User\ResetPasswordRequestForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * Provides all routes used inside the ResetPassword system
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[Route(path: "/mot-de-passe-oublie")]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $em,
        private UserRepository $userRepository
    ){}

    /** Display & process form to request a password reset. */
    #[Route(path: "", name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        if($user instanceof User) {
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(ResetPasswordRequestForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer
            );
        }
        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /** Confirmation page after a user has requested a password reset. */
    #[Route(path: "/check-email", name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }
        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /** Validates and process the reset URL that the user clicked in their email. */
    #[Route(path: "/reset/{token}", name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_reset_password');
        }
        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }
        try {
            /** @var User|UserInterface $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem validating your reset request - %s',
                $e->getReason()
            ));
            return $this->redirectToRoute('app_forgot_password_request');
        }
        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);
            // Encode(hash) the plain password, and set it.
            $encodedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($encodedPassword);
            $this->em->flush();
            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $emailFormData]);
        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            //$this->addFlash('reset_password_error', sprintf(
            //     'There was a problem handling your password reset request - %s',
            //     $e->getReason()
            //));
            return $this->redirectToRoute('app_forgot_password_request');
        }
        (new MailerController())->send($mailer, $user->getEmail(), "Demande de réinitialisation du mot de passe", [$resetToken->getToken(), "", "", "", "", ""], 'resetpassword');;
        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);
        return $this->redirectToRoute('app_check_email');
    }
}