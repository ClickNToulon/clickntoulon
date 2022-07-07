<?php

namespace App\Http\Seller\Controller;

use App\Domain\Product\ProductRepository;
use App\Domain\Shop\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    public function __construct(
        private readonly ShopRepository $shopRepository,
        private readonly ProductRepository $productRepository
    ){}

    #[Route(
        path: "",
        name: "home",
        requirements: ['subdomain' => 'commercants'],
        defaults: ['subdomain' => 'commercants'],
        host: '{subdomain}.clickntoulon.fr'
    )]
    public function index(): Response
    {
        return $this->render('seller/home.html.twig', [
            "menu" => "home"
        ]);
    }
}