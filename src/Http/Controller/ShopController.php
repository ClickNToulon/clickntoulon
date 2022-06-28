<?php

namespace App\Http\Controller;

use App\Domain\Shop\Payment;
use App\Domain\Shop\Shop;
use App\Domain\Shop\ShopRepository;
use App\Helper\FilterData\SearchShopData;
use App\Http\Form\Shop\FilterShopForm;
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
        private readonly ShopRepository $shopRepository,
        private readonly PaginatorInterface $paginator
    ){}

    #[Route(path: "", name: "index")]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $data = new SearchShopData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(FilterShopForm::class, $data);
        $form->handleRequest($request);
        $shops = $this->shopRepository->findSearch($data);
        return $this->render('shop/index.html.twig', [
            'shops' => $shops,
            'user' => $user,
            'form' => $form->createView(),
            "menu" => "shop"
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
            'hours' => $hours,
            "menu" => "shop"
        ]);
    }
}