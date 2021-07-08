<?php

namespace App\Controller\Admin;

use App\Form\SearchForm;
use App\Repository\BasketRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class AdminController extends AbstractController
{

    /**
     * @var ShopRepository
     */
    private ShopRepository $shopRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;
    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository, ProductRepository $productRepository, ShopRepository $shopRepository, PaginatorInterface $paginator) {

        $this->shopRepository = $shopRepository;
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/admin-test", name="admin_index")
     * @IsGranted("ROLE_ADMIN")
     * @param ChartBuilderInterface $chartBuilder
     * @param BasketRepository $basketRepository
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function index(ChartBuilderInterface $chartBuilder, BasketRepository $basketRepository, OrderRepository $orderRepository): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $total_users = count($this->userRepository->findAll());
        $total_shops = count($this->shopRepository->findAll());
        $total_products = count($this->productRepository->findAll());
        $total_baskets = count($basketRepository->findAll());
        $total_orders = count($orderRepository->findAll());
        $chart->setData([
            'labels' => ['Utilisateurs', 'Boutiques', 'Produits'],
            'datasets' => [
                [
                    'label' => 'Quantité',
                    'backgroundColor' => [
                        '#FFCD56',
                        '#FF5579',
                        '#059BFF'
                    ],
                    'borderColor' => 'rgba(0,0,0,0)',
                    'data' => [$total_users, $total_shops, $total_products]
                ],
            ]
        ]);
        $chart_other = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart_other->setData([
            'labels' => ['Paniers', 'Commandes'],
            'datasets' => [
                [
                    'label' => 'Quantité',
                    'backgroundColor' => [
                        '#FFCD56',
                        '#FF5579'
                    ],
                    'borderColor' => 'rgba(0,0,0,0)',
                    'data' => [$total_baskets, $total_orders]
                ]
            ]
        ]);
        return $this->render('admin/index.html.twig', [
            'user' => $this->getUser(),
            'chart' => $chart,
            'chart_other' => $chart_other
        ]);
    }

    /**
     * @Route("/admin-test/utilisateurs", name="admin_users")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function users(Request $request): Response
    {
        $users = $this->paginator->paginate(
            $this->userRepository->findAllQuery(),
            $request->query->getInt('page', 1),
            12
        );
        $total = count($users->getItems());
        return $this->render('admin/users.html.twig', [
            'user' => $this->getUser(),
            'users' => $users,
            'total' => $total
        ]);
    }

    /**
     * @Route("/admin-test/utilisateurs/{id}/bannir", name="admin_users_ban", requirements={"id": "[0-9\-]*"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return RedirectResponse
     */
    public function user_ban(Request $request): RedirectResponse
    {
        $user = $this->userRepository->find($request->attributes->get('id'));
        $user->setBanned(1);
        $this->em->persist($user);
        $this->em->flush();
        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("/admin-test/boutiques", name="admin_shops")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function shops(Request $request): Response
    {
        $shops = $this->paginator->paginate(
            $this->shopRepository->findAllQuery(),
            $request->query->getInt('page', 1),
            12
        );
        $total = count($shops->getItems());
        dump($shops);
        return $this->render('admin/shops.html.twig', [
            'user' => $this->getUser(),
            'shops' => $shops,
            'total' => $total
        ]);
    }

    /**
     * @Route("/admin-test/boutiques/{id}/bannir", name="admin_shops_ban", requirements={"id": "[0-9\-]*"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return RedirectResponse
     */
    public function shop_ban(Request $request): RedirectResponse
    {
        $shop = $this->userRepository->find($request->attributes->get('id'));
        $shop->setBanned(1);
        $this->em->persist($shop);
        $this->em->flush();
        return $this->redirectToRoute('admin_shops');
    }

    /**
     * @Route("/admin-test/produits", name="admin_products")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function products(Request $request): Response
    {
        $products = $this->paginator->paginate(
            $this->productRepository->findAllQuery(),
            $request->query->getInt('page', 1),
            12
        );
        $total = count($products->getItems());
        dump($products);
        return $this->render('admin/products.html.twig', [
            'user' => $this->getUser(),
            'products' => $products,
            'total' => $total
        ]);
    }

    /**
     * @Route("/admin-test/produits/{id}/supprimer", name="admin_products_delete", requirements={"id": "[0-9\-]*"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return RedirectResponse
     */
    public function product_delete(Request $request): RedirectResponse
    {
        $product = $this->userRepository->find($request->attributes->get('id'));
        $this->em->remove($product);
        $this->em->flush();
        return $this->redirectToRoute('admin_products');
    }

    /**
     * @Route("/admin-test/rechercher", name="admin_search")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $form = $this->createForm(SearchForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $_GET['q'] = $form->get('q')->getData();
        }

        if (isset($_GET['q'])) {
            $search_param = $_GET['q'];
        } else {
            $search_param = null;
        }

        $user = $this->getUser();
        $search_shop_results = $this->shopRepository->search($search_param);
        $search_product_results = $this->productRepository->search($search_param);
        $search_shop_count = count($search_shop_results);
        $search_product_count = count($search_product_results);
        $search_count = $search_shop_count + $search_product_count;
        return $this->render('admin/search.html.twig', [
            'search_shop_results' => $search_shop_results,
            'search_shop_count' => $search_shop_count,
            'search_product_results' => $search_product_results,
            'search_product_count' => $search_product_count,
            'search_count' => $search_count,
            'word_searched' => $search_param,
            'search' => $form->createView(),
            'user' => $user
        ]);
    }
}