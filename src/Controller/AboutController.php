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
        $user = $this->getUser();
        return $this->render('about/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/conditions", name="about_cgu")
     * @return Response
     */
    public function conditions(): Response
    {
        $user = $this->getUser();
        return $this->render('about/cgu.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/confidentialite", name="about_confidentialite")
     * @return Response
     */
    public function confidentialite(): Response
    {
        $user = $this->getUser();
        return $this->render('about/confidentialite.html.twig', [
            'user' => $user
        ]);
    }
}