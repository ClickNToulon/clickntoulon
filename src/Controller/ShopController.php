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

/**
 * Provides the routes for shops listing, unique shop view
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[Route(path: "/boutiques", name: "shop_")]
class ShopController extends AbstractController
{
    public function __construct(
        private ShopRepository $shopRepository,
        private PaginatorInterface $paginator
    ){}

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

    #[Route(path: "/{slug}", name: "show")]
    public function showOne(Shop $shop): Response
    {
        $user = $this->getUser();
        $payments_shop = $shop->getPayments();
        $payments_icons = new Payment();
        $payments = [];
        $hours = $shop->getFormattedWeekOpeningHours();
        foreach ($payments_shop as $payment_shop) {
            $payment = $payment_shop->getId();
            $payments[] = $payments_icons->getIcon($payment);
        }
        return $this->render('shop/show.html.twig', [
            'shop' => $shop,
            'user' => $user,
            'payments' => $payments,
            'hours' => $hours
        ]);
    }
}