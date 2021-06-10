<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Form\AddProductBasketType;
use App\Repository\BasketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @Route("/panier/ajout", name="basket_add")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function basket_add(Request $request): Response
    {
        $user = $this->getUser();
        $basket = new Basket();
        $baskets = $this->repository->findByUser($user->getId());
        $form = $this->createForm(AddProductBasketType::class, $basket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $done = false;
            foreach ($baskets as $b) {
                if ($b["shop_id"] == $form->get("shop_id")) {
                    $limit = count($b["products_id"]);
                    $product = explode(";", $b["products_id"]);
                    $quantity = explode(";", $b["quantity"]);
                    for ($i=0;$i<$limit;$i++) {
                        if($done != true) {
                            if($product[$i] == $form->get("products_id")) {
                                $done = true;
                                $quantity[$i] = $form->get("quantity");
                            }
                        }
                    }

                    if($done != true) {
                        array_push($product, $form->get("products_id"));
                        array_push($quantity, $form->get("quantity"));
                    }
                    $b["owner_id"] = $user->getId();
                    $b["products_id"] = implode(";", $product);
                    $b["quantity"] = implode(";", $quantity);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($b);
                    $entityManager->flush();
                }
            }
            if($done != true) {
                $basket = $form->getData();
                $basket->setOwnerId($user->getId());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($basket);
                $entityManager->flush();
            }
        }
        return $this->render('buyer/basket.html.twig', [
            'user' => $user,
            'baskets' => $baskets
        ]);
    }
}