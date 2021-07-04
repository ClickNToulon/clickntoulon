<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Shop;
use App\Form\CreateOrder;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function basket_add(Request $request, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $product_id = $request->attributes->get('id');
        $product = $productRepository->find($product_id);
        $baskets = $this->repository->findByUserAndShop($user->getId(), $product->getShopId());
        $quantity = $request->attributes->get('quantity');
        if($baskets == null) {
            return $this->redirectToRoute('add_to_new_basket', ['id' => $product->getId(), 'quantity' => $quantity]);
        } else {
            return $this->redirectToRoute('add_to_existing_basket', ['id' => $product->getId(), 'quantity' => $quantity, 'basket' => $baskets[0]->getId()]);
        }
    }

    /**
     * @Route("/panier/ajout/{basket}/{id}/{quantity}", name="add_to_existing_basket", requirements={"basket": "[0-9\-]*", "id": "[0-9\-]*", "quantity": "[0-9\-]*"})
     * @IsGranted("ROLE_USER")
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return RedirectResponse
     */
    public function add_to_existing_basket(ProductRepository $productRepository, Request $request): Response
    {
        $user = $this->getUser();
        $basket = $this->repository->find($request->attributes->get('basket'));
        $quantity = $request->attributes->get('quantity');
        $product_id = $request->attributes->get('id');
        $product = $productRepository->find($product_id);
        $basket_products = explode(",", $basket->getProductsId());
        $quantities = explode(",", $basket->getQuantity());
        if($basket->getOwnerId() == $user->getId()) {
            if (in_array($product->getId(), $basket_products)) {
                $index = array_search($product->getId(), $basket_products);
                $quantities[$index] = $quantity;
            } else if ($product->getShopId() == $basket->getShopId()) {
                array_push($basket_products, $product->getId());
                array_push($quantities, $quantity);
                $basket->setProductsId(implode(",", $basket_products));
            }
            $basket->setQuantity(implode(",", $quantities));
            $this->em->persist($basket);
            $this->em->flush();
            return $this->redirectToRoute('basket_index');
        }
    }

    /**
     * @Route("/panier/ajout/new/{id}/{quantity}", name="add_to_new_basket", requirements={"id": "[0-9\-]*", "quantity": "[0-9\-]*"})
     * @IsGranted("ROLE_USER")
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return RedirectResponse
     */
    public function add_to_new_basket(ProductRepository $productRepository, Request $request): Response
    {
        $user = $this->getUser();
        $product_id = $request->attributes->get('id');
        $product = $productRepository->find($product_id);
        $basket = new Basket();
        $quantity = $request->attributes->get('quantity');
        $basket->setOwnerId($user->getId())
            ->setShopId($product->getShopId())
            ->setQuantity($quantity)
            ->setProductsId($product->getId());
        $this->em->persist($basket);
        $this->em->flush();
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
        $basket = $this->repository->findByUserAndShop($user->getId(), $shop->getId());
        $products = [];
        $products_id = explode(",", $basket[0]->getProductsId());
        $quantities = explode(",", $basket[0]->getQuantity());
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