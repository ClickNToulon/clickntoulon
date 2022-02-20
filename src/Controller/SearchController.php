<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchForm;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides full text search on products and shops
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class SearchController extends AbstractController
{
    public function __construct(
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository
    ){}

    #[Route(path: "/recherche", name: "search")]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchForm::class);
        $form->handleRequest($request);
        $params = [];
        if($form->isSubmitted() && $form->isValid()) {
            $params = $request->request->all('search_form');
        }
        $searchParam = null;
        if($params["q"] !== null) {
            $searchParam = $params["q"];
        }
        /** @var User|UserInterface $user */
        $user = $this->getUser();
        $searchShops = $this->shopRepository->search($searchParam);
        $searchProducts = $this->productRepository->search($searchParam);
        $shopsCount = count($searchShops);
        $productsCount = count($searchProducts);
        $total = $shopsCount + $productsCount;
        return $this->render('search/index.html.twig', [
            'search_shop_results' => $searchShops,
            'search_shop_count' => $shopsCount,
            'search_product_results' => $searchProducts,
            'search_product_count' => $productsCount,
            'search_count' => $total,
            'word_searched' => $searchParam,
            'search' => $form->createView(),
            'user' => $user
        ]);
    }
}
