<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ProductController constructor.
     * @param ProductRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct(ProductRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/products", name="products.index")
     * @return Response
     */
    public function index(): Response
    {


        $products = $this->repository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }
}