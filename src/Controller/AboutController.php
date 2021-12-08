<?php


namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(path: "/a-propos", name: "about_index")]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('about/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @return Response
     */
    #[Route(path: "/conditions", name: "about_cgu")]
    public function conditions(): Response
    {
        $user = $this->getUser();
        return $this->render('about/cgu.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @return Response
     */
    #[Route(path: "/confidentialite", name: "about_confidentialite")]
    public function confidentialite(): Response
    {
        $user = $this->getUser();
        return $this->render('about/confidentialite.html.twig', [
            'user' => $user
        ]);
    }
}