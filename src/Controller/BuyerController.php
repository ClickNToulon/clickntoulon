<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Shop;
use App\Form\AddProductBasketType;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function basket(ShopRepository $shopRepository, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $baskets = $this->repository->findByUser($user->getId());
        $shops = [];
        $products = [];
        $quantities = [];
        foreach ($baskets as $b) {
            $shop_info = $shopRepository->find($b->getShopId());
            array_push($shops, $shop_info);
            $products_id = explode(";", $b->getProductsId());
            array_push($quantities, $b->getQuantity());
            foreach ($products_id as $p) {
                array_push($products, $productRepository->find($p));
            }
        }
        return $this->render('buyer/basket.html.twig', [
            'user' => $user,
            'baskets' => $baskets,
            'shops' => $shops,
            'products' => $products,
            'quantities' => $quantities
        ]);
    }

    /**
     * @Route("/panier/ajout", name="basket_add")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param ShopRepository $shop
     * @param ProductRepository $product
     * @return Response
     */
    public function basket_add(Request $request, ShopRepository $shop, ProductRepository $product): Response
    {
        $user = $this->getUser();
        $basket = new Basket();
        $baskets = $this->repository->findByUser($user->getId());
        $form = $this->createForm(AddProductBasketType::class, $basket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $done = false;
            $data = $form->getData();
            foreach ($baskets as $b) {
                if ($done != true) {
                    if ($b->getShopId() == $data->getShopId()) {
                        $b->setOwnerId($user->getId());
                        $products = explode(";", $b->getProductsId());
                        $quantity = explode(";", $b->getQuantity());
                        $limit = count($products);
                        for ($i = 0; $i < $limit; $i++) {
                            if ($products[$i] == $data->getProductsId()) {
                                $done = true;
                                $quantity[$i] = $data->getQuantity();
                            }
                        }
                        if ($done != true) {
                            array_push($products, $data->getProductsId());
                            array_push($quantity, $data->getQuantity());
                            $done = true;
                        }
                        $b->setProductsId(implode(";", $products));
                        $b->setQuantity(implode(";", $quantity));
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($b);
                        $entityManager->flush();
                    }
                }
            } if($done != true) {
                $basket = $form->getData();
                $basket->setOwnerId($user->getId());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($basket);
                $entityManager->flush();
            }
        }
        $shops = [];
        $products = [];
        $quantities = [];
        foreach ($baskets as $b) {
            $shop_info = $shop->find($b->getShopId());
            array_push($shops, $shop_info);
            $products_Id = explode(";", $b->getProductsId());
            $quantities = explode(";", $b->getQuantity());
            foreach ($products_Id as $p) {
                array_push($products, $product->find($p));
            }
        }
        return $this->render('buyer/basket.html.twig', [
            'user' => $user,
            'baskets' => $baskets,
            'shops'=> $shops,
            'products' => $products,
            'quantities' => $quantities
        ]);
    }
}