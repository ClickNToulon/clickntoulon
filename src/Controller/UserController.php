<?php

namespace App\Controller;

use App\Form\UpdatePasswordForm;
use App\Form\UpdateAvatarForm;
use App\Form\UpdateUserForm;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em) {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }

    /**
     * @Route("/profil", name="user_profil")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profil(): Response
    {
        $user = $this->getUser();
        return $this->render('user/profil.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/profil/modifier", name="user_edit")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        // Traitement du mot de passe
        [$formPassword, $response] = $this->createFormPassword($request);
        if ($response) {
            return $response;
        }

        [$formAvatar, $responseAvatar] = $this->createFormAvatar($request, $slugger);
        if ($responseAvatar) {
            return $responseAvatar;
        }

        [$formInfo, $responseInfo] = $this->createFormInfos($request);
        if ($responseInfo) {
            return $responseInfo;
        }

        return $this->render('user/edit.html.twig', [
            'form_password' => $formPassword->createView(),
            'form_avatar' => $formAvatar->createView(),
            'form_profile' => $formInfo->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/profil/mes-commandes", name="user_orders")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function orders(): Response
    {
        $user = $this->getUser();
        return $this->render('user/orders.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Génère le formulaire de création.
     * @param Request $request
     * @return array
     * @throws Exception
     */
    private function createFormPassword(Request $request): array
    {
        $form = $this->createForm(UpdatePasswordForm::class);
        $user = $this->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('password')->getData();
            $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data))
                ->setUpdatedAt(new DateTime('now', $dateTimeZoneFrance));
            $this->em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');

            return [$form, $this->redirectToRoute('user_edit')];
        }

        return [$form, null];
    }

    /**
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return array
     * @throws Exception
     */
    private function createFormAvatar(Request $request, SluggerInterface $slugger): array
    {
        $user = $this->getUser();
        $form = $this->createForm(UpdateAvatarForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $avatarFile = $form->get('avatar')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename, '-', 'fr');
                $newFilename = sprintf("%s-%s-%s.%s", $safeFilename, $user->getUsername(), $user->getId(), $avatarFile->guessExtension());

                // Move the file to the directory where brochures are stored
                try {
                    $avatarFile->move(
                        $this->getParameter('uploads'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
                $user->setAvatar($newFilename)
                    ->setUpdatedAt(new DateTime('now', $dateTimeZoneFrance));
            }

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Votre photo de profil a bien été mis à jour');
            return [$form, $this->redirectToRoute('user_edit')];
        }

        return [$form, null];
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    private function createFormInfos(Request $request): array
    {
        $user = $this->getUser();
        $form = $this->createForm(UpdateUserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
            $user->setUpdatedAt(new DateTime('now', $dateTimeZoneFrance));
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');

            return [$form, $this->redirectToRoute('user_edit')];
        }

        return [$form, null];
    }
}