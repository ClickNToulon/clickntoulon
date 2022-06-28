<?php

namespace App\Http\Controller;

use App\Domain\Product\ProductRepository;
use App\Domain\Shop\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Provides the home route
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class HomeController extends AbstractController
{
    public function __construct(
        private readonly ShopRepository $shopRepository,
        private readonly ProductRepository $productRepository
    ){}

    #[Route(path: "/", name: "home")]
    public function index(): Response
    {
        $shops = $this->shopRepository->home();
        $products = $this->productRepository->home();
        return $this->render('home.html.twig', [
            'shops' => $shops,
            'products' => $products,
            "menu" => "home"
        ]);
    }

    #[Route(path: "/test", name: "test")]
    public function test()
    {
        return $this->render("test.html.twig");
    }
}