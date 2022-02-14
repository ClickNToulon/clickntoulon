<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
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
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository
    ){}

    #[Route(path: "/", name: "home")]
    public function index(): Response
    {
        $shops = $this->shopRepository->home();
        $products = $this->productRepository->home();
        $user = $this->getUser();
        return $this->render('home.html.twig', [
            'shops' => $shops,
            'products' => $products,
            'user' => $user
        ]);
    }
}