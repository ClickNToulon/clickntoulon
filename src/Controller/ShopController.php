<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Shop;
use App\Repository\ShopRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/boutiques", name: "shop_")]
class ShopController extends AbstractController
{
    public function __construct(
        private ShopRepository $shopRepository,
        private PaginatorInterface $paginator
    ){}

    /**
     * @param Request $request
     * @return Response
     */
    #[Route(path: "", name: "index")]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $shops = $this->paginator->paginate(
            $this->shopRepository->findAllQuery(),
            $request->query->getInt('page', 1),
            8
        );
        return $this->render('shop/index.html.twig', [
            'shops' => $shops,
            'user' => $user
        ]);
    }

    /**
     * @param Shop $shop
     * @return Response
     */
    #[Route(path: "/{slug}", name: "show")]
    public function showOne(Shop $shop): Response
    {
        $user = $this->getUser();
        $payments_shop = $shop->getPayments();
        $payments_icons = new Payment();
        $payments = [];
        $hours = $shop->getFormattedWeekOpeningHours();
        foreach ($payments_shop as $k => $v) {
            $payment_shop = $v->getId();
            $payments[] = $payments_icons->getIcon($payment_shop);
        }
        return $this->render('shop/show.html.twig', [
            'shop' => $shop,
            'user' => $user,
            'payments' => $payments,
            'hours' => $hours
        ]);
    }
}