<?php


namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{

    /**
     * @Route("/a-propos", name="about_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('about/index.html.twig');
    }

    /**
     * @Route("/notre-equipe", name="about_team")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function team(UserRepository $userRepository): Response
    {
        $admins = $userRepository->findAdmins();
        return $this->render('about/team.html.twig', [
            'admins' => $admins
        ]);
    }

    /**
     * @Route("/conditions", name="about_cgu")
     * @return Response
     */
    public function conditions(): Response
    {
        return $this->render('about/cgu.html.twig');
    }

    /**
     * @Route("/confidentialite", name="about_cgu")
     * @return Response
     */
    public function confidentialite(): Response
    {
        return $this->render('about/confidentialite.html.twig');
    }
}