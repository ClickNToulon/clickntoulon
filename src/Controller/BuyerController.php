<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Product;
use App\Entity\Shop;
use App\Form\CreateOrder;
use App\Form\UpdateProductQuantityBasket;
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
     * @return Response
     */
    public function basket(ShopRepository $shopRepository, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $baskets = $this->repository->findByUser($user->getId());
        $shops = [];
        $products = [];
        $quantities = [];
        $products_id = [];
        $b_products_id = [];
        foreach ($baskets as $b) {
            $shop_info = $shopRepository->find($b->getShopId());
            $shops[$shop_info->getId()] = $shop_info;
            $products_id = explode(",", $b->getProductsId());
            $b_products_id[$b->getId()] = explode(",", $b->getProductsId());
            $quantities[$b->getId()] = explode(",", $b->getQuantity());
            foreach ($products_id as $p) {
                array_push($products, $productRepository->find($p));
            }
        }
        return $this->render('buyer/basket.html.twig', [
            'user' => $user,
            'baskets' => $baskets,
            'shops' => $shops,
            'products' => $products,
            'quantities' => $quantities,
            'b_products_id' => $b_products_id
        ]);
    }

    /**
     * @Route("/panier/ajout/{id}/{quantity}", name="basket_add", requirements={"id": "[0-9\-]*", "quantity": "[0-9\-]*"})
     * @IsGranted("ROLE_USER")
     * @param Product $product
     * @param Request $request
     * @return Response
     */
    public function basket_add(Product $product, Request $request): Response
    {
        $user = $this->getUser();
        $baskets = $this->repository->findByUser($user->getId());
        $quantity = $request->attributes->get('quantity');
        foreach ($baskets as $b) {
            $basket = $this->repository->find($b->getId());
            $basket_products = explode(",", $basket->getProductsId());
            $quantities = explode(",", $basket->getQuantity());
            if(in_array($product->getId(), $basket_products)) {
                $index = array_search($product->getId(), $basket_products);
                $product_quantity = $quantities[$index];
                $product_quantity = $product_quantity + $quantity;
                $quantities[$index] = $product_quantity;
            } else if($product->getShopId() == $b->getShopId()) {
                array_push($basket_products, $product->getId());
                array_push($quantities, $quantity);
                $b->setProductsId(implode(",", $basket_products));
            }
            $b->setQuantity(implode(",", $quantities));
            $this->em->persist($b);
            $this->em->flush();
        }
        return $this->render('buyer/add.html.twig', [
            'user' => $user,
            'baskets' => $baskets
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