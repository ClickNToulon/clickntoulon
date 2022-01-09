<?php

namespace App\Controller;

use App\Form\SearchForm;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository
    ){}

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/recherche', name: 'search')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchForm::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $_GET['q'] = $form->get('q')->getData();
        }
        if(isset($_GET['q']) && $_GET['q'] != null) {
            $search_param = $_GET['q'];
        } else {
            $search_param = null;
        }

        $user = $this->getUser();
        $search_shop_results = $this->shopRepository->search($search_param);
        $search_product_results = $this->productRepository->search($search_param);
        $search_shop_count = count($search_shop_results);
        $search_product_count = count($search_product_results);
        $search_count = $search_product_count + $search_shop_count;
        return $this->render('old_search/index.html.twig', [
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
