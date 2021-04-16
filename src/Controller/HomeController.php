<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home")
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ShopRepository $shopRepository, ProductRepository $productRepository): Response
    {
        $shops = $shopRepository->findAllVisible();
        $products = $productRepository->home();
        dump($products);
        $user = $this->getUser();
        return $this->render('home.html.twig', [
            'shops' => $shops,
            'products' => $products,
            'user' => $user
        ]);
    }

}