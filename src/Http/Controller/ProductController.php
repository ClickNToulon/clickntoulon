<?php

namespace App\Http\Controller;

use App\Domain\Buyer\BasketRepository;
use App\Domain\Product\Product;
use App\Domain\Product\ProductRepository;
use App\Domain\Shop\ShopRepository;
use App\Helper\FilterData\SearchProductData;
use App\Http\Form\Buyer\AddProductBasketForm;
use App\Http\Form\Product\FilterProductForm;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Provides the routes for products listing, unique product view and
 * checking if the product is already inside a basket
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[Route(path: "/produits", name: "product_")]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository  $productRepository,
        private readonly ShopRepository     $shopRepository,
        private readonly PaginatorInterface $paginator,
        private readonly BasketRepository $basketRepository
    ){}

    #[Route(path: "", name: "index")]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $shopSlug = $request->query->get('boutique');
        $data = new SearchProductData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(FilterProductForm::class, $data);
        $form->handleRequest($request);
        if ($shopSlug) {
            $data->shop = $this->shopRepository->findOneBy(['slug' => $shopSlug]);
        }
        $max = $this->productRepository->findMinMax($data);
        $products = $this->productRepository->findSearch($data);
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'max' => $max,
            "menu" => "product"
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route(path: "/{id}", name: "show")]
    public function shopProducts(Product $product): Response
    {
        $user = $this->getUser();
        $shop = $this->shopRepository->findById($product->getShop()->getId());
        $quantity = 1;
        if ($user != null) {
            $baskets = $this->basketRepository->findByUser($user);
            $quantity = $this->checkBaskets($baskets, $product);
        }
        $form = $this->createForm(AddProductBasketForm::class);
        return $this->render('product/show.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'shop' => $shop,
            "menu" => "product",
            'p_quantity' => $quantity
        ]);
    }

    private function checkBaskets(array $baskets, Product $product): int
    {
        $id = $product->getId();
        $inside = false;
        $return = 0;
        foreach ($baskets as $b) {
            if($b->getShop() == $product->getShop()) {
                $basket_products = $b->getProducts();
                for ($i = 0; $i < count($basket_products); $i++) {
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
}