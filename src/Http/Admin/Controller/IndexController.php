<?php

namespace App\Http\Admin\Controller;

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
        requirements: ['subdomain' => 'admin|dashboard'],
        defaults: ['subdomain' => 'admin'],
        host: '{subdomain}.clickntoulon.qbtl.dev'
    )]
    public function index(): Response
    {
        return $this->render('admin/home.html.twig', [
            "menu" => "home"
        ]);
    }

    #[Route(path: "/test", name: "test")]
    public function test()
    {
        return $this->render("test.html.twig");
    }

}