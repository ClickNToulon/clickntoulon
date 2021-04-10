<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil", name="user.profil")
     * @return Response
     */
    public function profil(): Response
    {
        return $this->render('user/profil.html.twig');
    }
}