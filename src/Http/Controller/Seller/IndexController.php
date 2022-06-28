<?php

namespace App\Http\Controller\Seller;

use App\Domain\Auth\User;
use App\Domain\Buyer\OrderRepository;
use App\Domain\Product\ProductRepository;
use App\Domain\Shop\Shop;
use App\Domain\Shop\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/ma-boutique", name: "seller_")]
class IndexController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ShopRepository         $shopRepository,
        private readonly ProductRepository      $productRepository,
        private readonly OrderRepository        $orderRepository,
        private readonly PaginatorInterface $paginator
    ){}

    #[Route(path: "/{id}", name: "index", requirements: ["id" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function index(Shop $shop, Request $request): Response
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            $shopsUser = $this->shopRepository->findAllByUser($user->getId());
            if (!in_array($shop, $shopsUser)) {
                return new Response($this->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
            }
        } else {
            return new Response($this->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
        }
        $orders = $this->orderRepository->getAllShop($shop)->getResult();
        $orders_buyers = [];
        foreach ($orders as $order) {
            $orders_buyers[$order->getId()] = $order->getBuyer();
        }
        $pagination_orders = $this->paginator->paginate(
            $this->orderRepository->getAllShop($shop),
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('seller/index.html.twig', [
            'shop' => $shop,
            'orders' => $pagination_orders,
            'orders_buyers' => $orders_buyers
        ]);
    }
}