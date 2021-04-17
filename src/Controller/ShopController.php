<?php


namespace App\Controller;


use App\Entity\Shop;
use App\Repository\ShopRepository;
use App\Repository\TimeTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{

    /**
     * @var ShopRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ShopRepository $repository, EntityManagerInterface $em)
    {

        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/boutiques", name="shop_index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $shops = $paginator->paginate(
            $this->repository->findAllQuery(),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('shop/index.html.twig', [
            'shops' => $shops,
            'user' => $user
        ]);
    }

    /**
     * @Route("/boutiques/{slug}", name="shop_show")
     * @param Shop $shop
     * @param TimeTableRepository $tableRepository
     * @return Response
     */
    public function showOne(Shop $shop, TimeTableRepository $tableRepository): Response
    {
        $user = $this->getUser();
        $timetable = $tableRepository->findById($shop);
        return $this->render('shop/show.html.twig', [
            'shop' => $shop,
            'timetable' => $timetable,
            'user' => $user
        ]);
    }

}