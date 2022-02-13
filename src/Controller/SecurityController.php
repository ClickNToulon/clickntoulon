<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Twig\Error\LoaderError;

class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ){}

    #[Route(path: "/connexion", name: "app_login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() && $this->getUser()->getIsVerified() !== false) {
            return $this->redirectToRoute('home');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_email' => $lastUsername, 'error' => $error]);
    }

    /**
     * @throws LogicException
     */
    #[Route(path: "/deconnexion", name: "app_logout")]
    public function logout()
    {
        throw new LogicException('Methode qui peut être nulle');
    }

    #[Route(path: "/inscription", name: "app_signup")]
    public function signUp(Request $request, UserPasswordHasherInterface $passwordEncoder, MailerInterface $mailer): RedirectResponse|Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $user = new User();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setPassword($passwordEncoder->hashPassword($user, $user->getPassword()));
                $this->em->persist($user);
                $this->em->flush();
                $title = "Veuillez vérifier votre compte chez ClickNToulon";
                $options = [];
                array_push($options, $user->getName(), $user->getId());
                (new MailerController)->send($mailer, $user->getEmail(), $title, $options, 'signup');
                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning', "Nom d'utilisateur ou adresse mail déjà utilisé");
            }
            //$this->addFlash('warning', "L'inscription sur ClickNToulon n'est pas encore ouverte.");
        }
        return $this->render('security/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: "/verifier/profil/{id}", name: "app_verify_email", requirements: ["id" => "[0-9\-]*"])]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
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
        if($user->getIsVerified() == true) {
            $this->addFlash('warning', 'Votre compte a déjà été vérifié ou ce compte n\'existe pas. Veuillez ressayer ultérieurement.');
        } else {
            $user->setIsVerified(1);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Votre compte a bien été vérifié. Vous pouvez désormais accéder à tout le contenu de la plateforme TouSolidaires.');
        }
        return $this->render('security/verify_email.html.twig', [
            'user' => $user,
        ]);
    }
}