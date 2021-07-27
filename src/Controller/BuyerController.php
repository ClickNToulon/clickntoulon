<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Order;
use App\Entity\Shop;
use App\Form\CreateOrder;
use App\Form\UpdateProductQuantityBasket;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
    private EntityManagerInterface $em;

    public function __construct(BasketRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/panier", name="basket_index")
     * @IsGranted("ROLE_USER")
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     */
    public function basket(ShopRepository $shopRepository, ProductRepository $productRepository, Request $request): Response
    {
        $user = $this->getUser();
        $baskets = $this->repository->findByUser($user->getId());
        $shops = [];
        $products = [];
        $quantities = [];
        $products_id = [];
        foreach ($baskets as $b) {
            dump($b->getProductsId());
            $shop_info = $shopRepository->find($b->getShopId());
            $shops[$shop_info->getId()] = $shop_info;
            $products_id = explode(",", $b->getProductsId());
            $quantities = explode(",", $b->getQuantity());
            foreach ($products_id as $p) {
                array_push($products, $productRepository->find($p));
            }
        }
        /*dump($baskets);
        $form = $this->createForm(UpdateProductQuantityBasket::class, $baskets[0]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            dump($data);
            dump($request);
        }*/
        return $this->render('buyer/basket.html.twig', [
            'user' => $user,
            'baskets' => $baskets,
            'shops' => $shops,
            'products' => $products,
            'quantities' => $quantities,
            //'form' => $form->createView()
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
        $form = $this->createForm(UpdateProductQuantityBasket::class, $basket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $done = false;
            $data = $form->getData();
            foreach ($baskets as $b) {
                if ($done != true) {
                    if ($b->getShopId() == $data->getShopId()) {
                        $b->setOwnerId($user->getId());
                        $products = explode(",", $b->getProductsId());
                        $quantity = explode(",", $b->getQuantity());
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
                        $b->setProductsId(implode(",", $products));
                        $b->setQuantity(implode(",", $quantity));
                        $this->em->persist($b);
                        $this->em->flush();
                    }
                }
            } if($done != true) {
                $basket = $form->getData();
                $basket->setOwnerId($user->getId());
                $this->em->persist($basket);
                $this->em->flush();
            }
        }
        $shops = [];
        $products = [];
        $quantities = [];
        foreach ($baskets as $b) {
            $shop_info = $shop->find($b->getShopId());
            array_push($shops, $shop_info);
            $products_Id = explode(",", $b->getProductsId());
            $quantities = explode(",", $b->getQuantity());
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

    /**
     * @Route("/panier/reglement/{id}", name="basket_checkout", requirements={"id": "[0-9\-]*"})
     * @IsGranted("ROLE_USER")
     * @param Shop $shop
     * @param Request $request
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function checkout(Shop $shop, Request $request, ShopRepository $shopRepository, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $basket = $this->repository->findOneByShop($shop->getId());
        $products = [];
        $quantities = [];
        dump($basket);
        $products_id = explode(",", $basket[0]->getProductsId());
        $quantities = explode(',', $basket[0]->getQuantity());
        foreach ($products_id as $p) {
            array_push($products, $productRepository->find($p));
        }
        $total_products = count($products);
        $form = $this->createForm(CreateOrder::class, null);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $order_quantities = implode(",", $quantities);
            $order_products_id = implode(",", $products_id);
            $data->setStatus(0)
                ->setQuantity($order_quantities)
                ->setProductsId($order_products_id)
                ->setTimeBegin(null)
                ->setTimeEnd(null)
                ->setBuyerId($user->getId())
                ->setBasketId($basket[0]->getId());
            $this->em->persist($data);
            $this->em->remove($basket[0]);
            $this->em->flush();
            return $this->redirectToRoute('user_order', ['id' => $data->getId()]);
        }
        return $this->render('buyer/checkout.html.twig', [
            'user' => $user,
            'basket' => $basket,
            'shop'=> $shop,
            'products' => $products,
            'quantities' => $quantities,
            'total_products' => $total_products,
            'form' => $form->createView()
        ]);
    }
}