<?php

namespace App\Controller;

use App\Repository\BasketRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuyerController extends AbstractController
{

    /**
     * @var BasketRepository
     */
    private BasketRepository $repository;

    public function __construct(BasketRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/panier", name="basket_index")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function basket(): Response
    {
        $user = $this->getUser();
        $baskets = $this->repository->findByUser($user->getId());
        dump($baskets);
        return $this->render('buyer/basket.html.twig', [
            'user' => $user,
            'baskets' => $baskets
        ]);
    }
}