<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Product;
use App\Form\AddProductBasketForm;
use App\Repository\BasketRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/produits", name: "product_")]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private ShopRepository $shopRepository,
        private PaginatorInterface $paginator,
        private BasketRepository $basketRepository,
        private CategoryRepository $categoryRepository
    ){}

    /**
     * @param Request $request
     * @return Response
     */
    #[Route(path: "", name: "index")]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $shopSlug = $request->query->get('boutique');
        $query = $this->productRepository->findAllQuery();
        if ($shopSlug) {
            $shop = $this->shopRepository->findOneBy(['slug' => $shopSlug]);
            if (null !== $shop) {
                $query = $this->productRepository->findAllByShopQuery($shop);
            }
        }
        $products = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'user' => $user
        ]);
    }

    /**
     * @param array $baskets
     * @param Product $product
     * @return int
     */
    public function checkBaskets(array $baskets, Product $product): int
    {
        $id = $product->getId();
        $inside = false;
        $return = 0;
        foreach ($baskets as $b) {
            if($b->getShop() == $product->getShop()) {
                $basket_products = $b->getProducts();
                $limit = count($basket_products);
                for ($i = 0; $i < $limit; $i++) {
                    if($basket_products[$i]->getId() == $id) {
                        $inside = true;
                        $quantities = $b->getQuantity();
                        $return = (int)$quantities[$i] + 1;
                    }
                }
            }
        }
        if ($inside == false) {
            $return = 1;
        }
        return $return;
    }

    /**
     * @param Product $product
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route(path: "/{id}", name: "show")]
    public function shopProducts(Product $product): Response
    {
        $user = $this->getUser();
        $shop = $this->shopRepository->findById($product->getShop()->getId());
        $payments_shop = $shop->getPayments();
        $payments_icons = new Payment();
        $payments = [];
        foreach ($payments_shop as $k => $v) {
            $payment_shop = $v->getId();
            $payments[] = $payments_icons->getIcon($payment_shop);
        }
        $categories = [];
        $categories_shop = $shop->getCategories();
        foreach ($categories_shop as $row => $id) {
            $category = $this->categoryRepository->find($id);
            $categories[] = $category->getName();
        }
        if ($user != null) {
            $baskets = $this->basketRepository->findByUser($user);
            $quantity = $this->checkBaskets($baskets, $product);
        } else {
            $quantity = 1;
        }
        $form = $this->createForm(AddProductBasketForm::class);
        return $this->render('product/show.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'shop' => $shop,
            'user' => $user,
            'payments' => $payments,
            'categories' => $categories,
            'p_quantity' => $quantity
        ]);
    }
}