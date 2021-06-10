<?php


namespace App\Controller;


use App\Entity\Basket;
use App\Entity\Category;
use App\Entity\Payment;
use App\Entity\Product;
use App\Form\AddProductBasketType;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
    /**
     * @var BasketRepository
     */
    private $basketRepository;

    public function __construct(ProductRepository $repository, PaginatorInterface $paginator, basketrepository $basketRepo)
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->basketRepository = $basketRepo;
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
     * @param array $baskets
     * @param Product $product
     * @return int
     */
    public function checkBaskets(array $baskets, Product $product): int {
        $id = $product->getId();
        $inside = false;
        $return = 0;
        foreach ($baskets as $b) {
            if($b->getShopId() == $product->getShopId()) {
                $products = $b->getProductsId();
                $products_array = explode(";", $products);
                $limit = count($products_array);
                for ($i = 0; $i < $limit; $i++) {
                    if ($inside == false) {
                        if ($products_array[$i] == $id) {
                            $inside = true;
                            $quantities = $b->getQuantity();
                            $quantity = explode(";", $quantities);
                            $return = (int)$quantity[$i] + 1;
                        }
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
        $form = $this->createForm(AddProductBasketType::class, new Basket());
        if ($user != null) :
        $baskets = $this->basketRepository->findByUser($user->getId());
        $quantity = $this->checkBaskets($baskets, $product);
        else:
            $quantity = 1;
        endif;
        return $this->render('product/show.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'shop' => $shop,
            'user' => $user,
            'payments' => $payments,
            'categories' => $categories,
            'quantity' => $quantity
        ]);
    }

}