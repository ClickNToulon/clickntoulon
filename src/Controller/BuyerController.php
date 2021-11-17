<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Order;
use App\Entity\Shop;
use App\Form\AddProductBasketForm;
use App\Form\CreateOrderForm;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
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
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function basket(ShopRepository $shopRepository, ProductRepository $productRepository, Request $request, BasketRepository $basketRepository): Response
    {
        if($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $basket = $basketRepository->find($data['basket_id']);
            $basket_products = $basket->getProducts();
            foreach ($basket_products as $bp) {
                $form_quantities[$bp] = $data['quantity_'. $bp];
            }
            $basket->setQuantity($form_quantities);
            $this->em->persist($basket);
            $this->em->flush();
        }
        $user = $this->getUser();
        $baskets = $this->repository->findByUser($user);
        $shops = [];
        $products = [];
        $quantities = [];
        foreach ($baskets as $b) {
            $shop_info = $shopRepository->find($b->getShopId());
            $shops[$shop_info->getId()] = $shop_info;
            $products_id = explode(",", $b->getProducts());
            $quantities[$b->getId()] = explode(",", $b->getQuantity());
            $products[$b->getId()] = [];
            foreach ($products_id as $p) {
                array_push($products[$b->getId()], $productRepository->find($p));
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
        $data = $request->request->all();
        $data = $data['add_product_basket'];
        $done = false;
        /*if($baskets !== []) {
            foreach ($baskets as $b) {
                if ($done != true) {
                    if ($b->getShopId() == $data['shop_id']) {
                        $b->setOwnerId($user->getId());
                        $products = explode(",", $b->getProductsId());
                        $quantity = explode(",", $b->getQuantity());
                        $limit = count($products);
                        for ($i = 0; $i < $limit; $i++) {
                            if ($products[$i] == $data['products_id']) {
                                $done = true;
                                $quantity[$i] = $data['quantity'];
                            }
                        }
                        if ($done != true) {
                            array_push($products, $data['products_id']);
                            array_push($quantity, $data['quantity']);
                            $done = true;
                        }
                        $b->setProductsId(implode(",", $products));
                        $b->setQuantity(implode(",", $quantity));
                        $this->em->persist($b);
                        $this->em->flush();
                    }
                }
                if($done != true) {
                    dump($data);
                    $basket  = new Basket();
                    $basket->setQuantity($data['quantity'])
                        ->setProductsId($data['products_id'])
                        ->setOwnerId($user->getId())
                        ->setShopId($data['shop_id']);
                    $this->em->persist($basket);
                    $this->em->flush();
                }
            }
        } else {
            $basket  = new Basket();
            $basket->setQuantity($data['quantity'])
                ->setProductsId($data['products_id'])
                ->setOwnerId($user->getId())
                ->setShopId($data['shop_id']);
            $this->em->persist($basket);
            $this->em->flush();
        }*/
        return $this->redirectToRoute('basket_index');
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
        $basket = $this->repository->findByUserAndShop($user, $shop);
        $basket = $basket[0];
        $products = [];
        $quantities = [];
        $products_id = explode(",", $basket->getProductsId());
        $quantities = explode(',', $basket->getQuantity());
        foreach ($products_id as $p) {
            array_push($products, $productRepository->find($p));
        }
        $total_products = count($products);
        $form = $this->createForm(CreateOrderForm::class, null);
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
                ->setBasketId($basket->getId());
            $this->em->persist($data);
            $this->em->remove($basket);
            $this->em->flush();
            return $this->redirectToRoute('user_order', ['number' => $data->getNumber()]);
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