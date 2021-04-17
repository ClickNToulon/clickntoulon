<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuyerController extends AbstractController
{
    /**
     * @Route("/panier", name="basket_index")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function basket(): Response
    {
        $user = $this->getUser();
        return $this->render('buyer/basket.html.twig', [
            'user' => $user
        ]);
    }
}