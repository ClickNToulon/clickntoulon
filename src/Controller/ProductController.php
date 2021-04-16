<?php


namespace App\Controller;


use App\Repository\ProductRepository;
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

    public function __construct(ProductRepository $repository)
    {

        $this->repository = $repository;
    }

    /**
     * @Route("/produits", name="product_index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $pagination = $paginator->paginate(
            $this->repository->findAllQuery(),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
            'user' => $user
        ]);
    }

}