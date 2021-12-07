<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Shop;
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

#[Route(path: "/panier", name: "basket_")]
class BuyerController extends AbstractController
{
    public function __construct(
        private BasketRepository $basketRepository,
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository,
        private EntityManagerInterface $em
    ){}

    /**
     * @param Request $request
     * @return Response
     */
    #[
        Route(path: "", name: "index"),
        IsGranted("ROLE_USER")
    ]
    public function basket(Request $request): Response
    {
        if($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $basket = $this->basketRepository->find($data['basket_id']);
            $basket_products = $basket->getProducts();
            foreach ($basket_products as $bp) {
                $form_quantities[$bp->getId()] = $data['quantity_'. $bp->getId()];
            }
            $basket->setQuantity($form_quantities);
            $this->em->persist($basket);
            $this->em->flush();
        }
        $user = $this->getUser();
        $baskets = $this->basketRepository->findByUser($user);
        $shops = [];
        $products = [];
        $quantities = [];
        foreach ($baskets as $b) {
            $basket_products = $b->getProducts();
            $basket_quantities = $b->getQuantity();
            $quantities[$b->getId()] = [];
            foreach ($basket_quantities as $quantity) {
                $quantities[$b->getId()][] = $quantity;
            }
            $products[$b->getId()] = [];
            foreach ($basket_products as $p) {
                $shops[$b->getId()] = $p->getShop();
                array_push($products[$b->getId()], $this->productRepository->find($p->getId()));
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
     * @param Request $request
     * @return Response
     */
    #[
        Route(path: "/ajout", name: "add"),
        IsGranted("ROLE_USER")
    ]
    public function basket_add(Request $request): Response
    {
        $user = $this->getUser();
        $baskets = $this->basketRepository->findByUser($user);
        $data = $request->request->all();
        $data = $data['add_product_basket_form'];
        $done = false;
        $quantities = [];
        if($baskets !== []) {
            foreach ($baskets as $b) {
                if ($done != true) {
                    if ($b->getShop()->getId() == $data['shop_id']) {
                        $b->setOwner($user);
                        $products = $b->getProducts();
                        $product = $this->productRepository->find($data['product_id']);
                        $quantity = $b->getQuantity();
                        foreach ($products as $p) {
                            foreach ($quantity as $q) {
                                $quantities[$p->getId()] = $q;
                            }
                            if($p->getId() == $data['product_id']) {
                                $done = true;
                                $quantities[$p->getId()] = $data['quantity'];
                            }
                        }
                        if ($done != true) {
                            array_push($products, $product);
                            array_push($quantity, $data['quantity']);
                            $done = true;
                        }
                        foreach ($products as $add_product) {
                            $b->addProduct($add_product);
                        }
                        $b->setQuantity($quantity);
                        $this->em->persist($b);
                        $this->em->flush();
                    }
                }
                if($done != true) {
                    $basket  = new Basket();
                    $basket->setQuantity([$data['quantity']])
                        ->addProduct($this->productRepository->find($data['product_id']))
                        ->setOwner($user)
                        ->setShop($this->shopRepository->find($data['shop_id']));
                    $this->em->persist($basket);
                    $this->em->flush();
                }
            }
        } else {
            $basket  = new Basket();
            $basket->setQuantity([$data['quantity']])
                ->setOwner($user)
                ->addProduct($this->productRepository->find($data['product_id']))
                ->setShop($this->shopRepository->find($data['shop_id']));
            $this->em->persist($basket);
            $this->em->flush();
        }
        return $this->redirectToRoute('basket_index');
    }

    /**
     * @param Shop $shop
     * @param Request $request
     * @return Response
     */
    #[
        Route(path: "/reglement/{id}", name: "checkout", requirements: ["id" => "[0-9\-]*"]),
        IsGranted("ROLE_USER")
    ]
    public function checkout(Shop $shop, Request $request): Response
    {
        $user = $this->getUser();
        $basket = $this->basketRepository->findByUserAndShop($user, $shop);
        $basket = $basket[0];
        $products = [];
        $basket_products = $basket->getProducts();
        $basket_quantities = $basket->getQuantity();
        $quantities = $basket_quantities;
        foreach ($basket_products as $p) {
            array_push($products, $this->productRepository->find($p->getId()));
        }
        $total_products = count($products);
        $form = $this->createForm(CreateOrderForm::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $order_quantities = $basket_quantities;
            $order_products = $basket_products;
            foreach ($order_products as $op) {
                $data->addProduct($op);
            }
            $data->setStatus(0)
                ->setShop($shop)
                ->setQuantity($order_quantities)
                ->setBuyer($user);
            $this->em->persist($data);
            $this->em->remove($basket);
            $this->em->flush();
            return $this->redirectToRoute('user_order', ['number' => $data->getOrderNumber()]);
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