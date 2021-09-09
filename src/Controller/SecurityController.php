<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Twig\Error\LoaderError;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
             return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/inscription", name="app_signup")
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $em
     * @param MailerInterface $mailer
     * @return RedirectResponse|Response
     * @throws LoaderError
     */
    public function signUp(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, MailerInterface $mailer)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
                $em->persist($user);
                $em->flush();
                $title = "Veuillez vérifier votre compte chez TouSolidaires";
                $options = [];
                array_push($options, $user->getFullName(), $user->getId());
                (new MailerController)->send($mailer, $user->getEmail(), $title, $options, 'signup');
                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning', "Nom d'utilisateur ou adresse mail déjà utilisé");
            }
        }
        return $this->render('security/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/verifier/profil/{id}", name="app_verify_email", requirements={"id": "[0-9\-]*"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $id = $request->attributes->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_signup');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_signup');
        }
        // validate email confirmation link, sets User::isVerified=true and persists
        if($user->IsVerified() == true) {
            $this->addFlash('warning', 'Votre compte a déjà été vérifié ou ce compte n\'existe pas. Veuillez ressayer ultérieurement.');
            return $this->render('security/verify_email.html.twig', [
                'user' => $user,
            ]);
        } else {
            $user->setIsVerified(1);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a bien été vérifié. Vous pouvez désormais accéder à tout le contenu de la plateforme TouSolidaires.');
            return $this->render('security/verify_email.html.twig', [
                'user' => $user,
            ]);
        }
    }
}