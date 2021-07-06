<?php


namespace App\Controller;


use App\Entity\Payment;
use App\Entity\Shop;
use App\Repository\PaymentRepository;
use App\Repository\ShopRepository;
use App\Repository\TimeTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
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
    private ShopRepository $repository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

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
            8
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
     * @param PaymentRepository $paymentRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showOne(Shop $shop, TimeTableRepository $tableRepository, PaymentRepository $paymentRepository): Response
    {
        $user = $this->getUser();
        $timetable = $tableRepository->findById($shop);
        dump($timetable);
        $payments_shop = $shop->getPayments();
        $payments_icons = new Payment();
        $payments = [];
        foreach ($payments_shop as $k => $v) {
            $payment_shop = $v->getId();
            $payments[] = $payments_icons->getIcon($payment_shop);
        }
        return $this->render('shop/show.html.twig', [
            'shop' => $shop,
            'timetable' => $timetable,
            'user' => $user,
            'payments' => $payments
        ]);
    }

}