<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Shop;
use App\Entity\User;
use App\Form\CreateOrderForm;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides all routes for the basket and order checkout
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[Route(path: "/panier", name: "basket_")]
class BuyerController extends AbstractController
{
    public function __construct(
        private BasketRepository $basketRepository,
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository,
        private EntityManagerInterface $em
    ){}

    #[Route(path: "", name: "index")]
    #[IsGranted("ROLE_USER")]
    public function basket(Request $request): Response
    {
        if($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $basket = $this->basketRepository->find($data['basket_id']);
            $basket_products = $basket->getProducts();
            foreach ($basket_products as $bp) {
                if($data['quantity_'. $bp->getId()] != 0 && $data['quantity_'. $bp->getId()] != null) {
                    $form_quantities[] = $data['quantity_'. $bp->getId()];
                } else {
                    $basket->removeProduct($bp);
                }
            }
            if(isset($form_quantities) && $form_quantities != []) {
                $basket->setQuantity($form_quantities);
                $this->em->persist($basket);
            } else {
                $this->em->remove($basket);
            }
            $this->em->flush();
        }
        $user = $this->getUser();
        $baskets = $this->basketRepository->findByUser($user);
        [$shops, $quantities, $products] = $this->getInfosFromBasket($baskets);
        return $this->render('buyer/basket.html.twig', [
            'user' => $user,
            'baskets' => $baskets,
            'shops' => $shops,
            'products' => $products,
            'quantities' => $quantities
        ]);
    }

    #[Route(path: "/ajout", name: "add")]
    #[IsGranted("ROLE_USER")]
    public function basket_add(Request $request): Response
    {
        $user = $this->getUser();
        $baskets = $this->basketRepository->findByUser($user);
        $data = $request->request->all('add_product_basket_form');
        $updated = false;
        $product = $this->productRepository->find($data['product_id']);
        if($baskets !== []) {
            foreach ($baskets as $basket) {
                if($basket->getShop()->getId() == $data['shop_id']) {
                    $basket_quantities = $basket->getQuantity();
                    $basket_products = $basket->getProducts();
                    if(!in_array($product, $basket_products->getValues())) {
                        $basket->addProduct($product);
                    }
                    $key = array_search($product, $basket_products->getValues());
                    $basket_quantities[$key] = $data['quantity'];
                    $basket->setQuantity($basket_quantities);
                    $this->em->persist($basket);
                    $this->em->flush();
                    $updated = true;
                }
            }
            if($updated !== true) {
                $this->createBasketFromInfos($data, $user);
            }
        } else {
            $this->createBasketFromInfos($data, $user);
        }
        return $this->redirectToRoute('basket_index');
    }

    #[Route(path: "/reglement/{id}", name: "checkout", requirements: ["id" => "[0-9\-]*"])]
    #[IsGranted("ROLE_USER")]
    public function checkout(Shop $shop, Request $request): Response
    {
        $user = $this->getUser();
        try {
            $basket = $this->basketRepository->findByUserAndShop($user, $shop);
            if ($basket === null) {
                return new Response($this->renderView('bundles/TwigBundle/Exception/error404.html.twig'), Response::HTTP_NOT_FOUND);
            }
        } catch (NonUniqueResultException $exception) {
            return new Response($this->renderView('bundles/TwigBundle/Exception/error404.html.twig'), Response::HTTP_NOT_FOUND);
        }
        $products = [];
        $basket_products = $basket->getProducts();
        $basket_quantities = $basket->getQuantity();
        foreach ($basket_products as $p) {
            $products[] = $this->productRepository->find($p->getId());
        }
        $total_products = count($products);
        $form = $this->createForm(CreateOrderForm::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            try {
                $now = new DateTime('now', new DateTimeZone("Europe/Paris"));
                if($now >= $data->getDay()) {
                    $this->addFlash('warning', 'La date de retrait souhaitée se situe dans le passé. Veuillez réessayer avec une date correcte.');
                } else {
                    $order_quantities = $basket_quantities;
                    $order_products = $basket_products;
                    foreach ($order_products as $op) {
                        $data->addProduct($op);
                    }
                    $data
                        ->setStatus(0)
                        ->setShop($shop)
                        ->setQuantity($order_quantities)
                        ->setBuyer($user);
                    $this->em->persist($data);
                    $this->em->remove($basket);
                    $this->em->flush();
                    $this->addFlash('success', 'La commande a bien été passée');
                    return $this->redirectToRoute('user_order', ['number' => $data->getOrderNumber()]);
                }
            } catch (Exception) {
                return new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Une erreur est survenue. Veuillez réessayer plus tard.');
        }
        return $this->render('buyer/checkout.html.twig', [
            'user' => $user,
            'basket' => $basket,
            'shop'=> $shop,
            'products' => $products,
            'quantities' => $basket_quantities,
            'total_products' => $total_products,
            'form' => $form->createView()
        ]);
    }

    private function getInfosFromBasket(array $baskets): array
    {
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
                $products[$b->getId()][] = $this->productRepository->find($p->getId());
            }
        }
        return [$shops, $quantities, $products];
    }

    private function createBasketFromInfos(array $data, User|UserInterface $user)
    {
        $basket = new Basket();
        $basket->setQuantity([$data['quantity']])
            ->addProduct($this->productRepository->find($data['product_id']))
            ->setOwner($user)
            ->setShop($this->shopRepository->find($data['shop_id']));
        $this->em->persist($basket);
        $this->em->flush();
    }
}