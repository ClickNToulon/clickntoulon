<?php


namespace App\Controller;


use App\Entity\Product;
use App\Entity\Shop;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    /**
     * @var ProductRepository
     */
    private $repository;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ProductRepository $repository, PaginatorInterface $paginator)
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/produits", name="product_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $products = $this->paginator->paginate(
            $this->repository->findAllQuery(),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'user' => $user
        ]);
    }

    /**
     * @Route("/produits/{id}", name="product_show")
     * @param Product $product
     * @return Response
     */
    public function shopProducts(Product $product, ShopRepository $shopRepository): Response
    {
        $user = $this->getUser();
        $shop = $shopRepository->findById($product->getShopId());
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'shop' => $shop,
            'user' => $user
        ]);
    }

}