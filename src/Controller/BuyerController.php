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

    private ShopRepository $shop_repo;

    private ProductRepository $product_repo;

    public function __construct(BasketRepository $repository, ShopRepository $shop_repo, ProductRepository $product_repo)
    {
        $this->repository = $repository;
        $this->shop_repo = $shop_repo;
        $this->product_repo = $product_repo;
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
        $shops = [];
        $products = [];
        foreach ($baskets as $b) {
            $shop_info = $this->shop_repo->find($b->getShopId());
            array_push($shops, $shop_info);
            $products_Id = explode(";", $b->getProductsId());
            foreach ($products_Id as $product) {
                array_push($products, $this->product_repo->find($product));
            }
        }
        return $this->render('buyer/basket.html.twig', [
            'user' => $user,
            'baskets' => $baskets,
            'shops' => $shops,
            'products' => $products
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
                $data = $form->getData();
                if ($b->getShopId() == $data->getShopId()) {
                    $b->setOwnerId($user->getId());
                    if (is_array($b->getProductsId())) {
                        $limit = count($b->getProductsId());
                        $product = explode(";", $b->getProductsId());
                        $quantity = explode(";", $b->getQuantity());
                        for ($i=0;$i<$limit;$i++) {
                            if($done != true) {
                                if($product[$i] == $data->getProductsId()) {
                                    $done = true;
                                    $quantity[$i] = $data->getQuantity();
                                }
                            }
                        }
                        $b->setProductsId(implode(";", $product));
                        $b->setQuantity(implode(";", $quantity));
                    } else {
                        $b->setQuantity($data->getQuantity());
                        $done = true;
                    }

                    if($done != true) {
                        if(is_array($b->getProductsId())) {
                            $product = explode(";", $b->getProductsId());
                            $quantity = explode(";", $b->getQuantity());
                            array_push($product, $form->get("products_id"));
                            array_push($quantity, $form->get("quantity"));
                            $b->setProductsId(implode(";", $product));
                            $b->setQuantity(implode(";", $quantity));
                            $done = true;
                        } else {
                            $product = [];
                            $quantity = [];
                            array_push($product, $b->getProductsId(), $data->getProductsId());
                            array_push($quantity, $b->getQuantity(), $data->getQuantity());
                            $b->setProductsId(implode(";", $product));
                            $b->setQuantity(implode(";", $quantity));
                            $done = true;
                        }
                    }

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