<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\Shop;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * @param ShopRepository $shopRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function shopProducts(Product $product, ShopRepository $shopRepository, CategoryRepository $categoryRepository): Response
    {
        $user = $this->getUser();
        $shop = $shopRepository->findById($product->getShopId());
        $payments_shop = $shop->getPayments();
        $payments_icons = new Payment();
        $categories_entity = new Category();
        $payments = [];
        foreach ($payments_shop as $k => $v) {
            $payment_shop = $v->getId();
            $payments[] = $payments_icons->getIcon($payment_shop);
        }
        $categories = [];
        $categories_shop = $shop->getCategories();
        foreach ($categories_shop as $row => $id) {
            $category = $categoryRepository->find($id);
            $categories[] = $category->getName();
        }
        dump($categories);
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'shop' => $shop,
            'user' => $user,
            'payments' => $payments,
            'categories' => $categories
        ]);
    }

}