<?php

namespace App\Http\Seller\Controller;

use App\Domain\Auth\User;
use App\Domain\Buyer\OrderRepository;
use App\Domain\Product\ProductRepository;
use App\Domain\Shop\Shop;
use App\Domain\Shop\ShopRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class IndexController extends AbstractController
{

    public function __construct(
        private readonly ShopRepository $shopRepository,
        private readonly ProductRepository $productRepository,
        private readonly OrderRepository $orderRepository
    ){}

    /**
     * @throws Exception
     */
    #[Route(
        path: "",
        name: "home",
        requirements: ['subdomain' => 'commercants'],
        defaults: ['subdomain' => 'commercants'],
        host: '{subdomain}.clickntoulon.fr'
    )]
    #[IsGranted("ROLE_MERCHANT")]
    public function index(Shop $shop): Response
    {
        /** @var User|UserInterface $user */
        $user = $this->getUser();
        $orders = $shop->getOrders();
        $products = $shop->getProducts();
        $orders_test = count($this->orderRepository->findAllPriorTodayForShop($shop, new DateTime('now', new DateTimeZone("Europe/Paris"))));
        $total_orders = count($orders);
        $percentage = 0;
        if ($total_orders > 0) {
            $percentage = round((abs($orders_test - $total_orders)/(($total_orders + $orders_test)/2)) * 100, 1);
        }
        $shopsOwned = $this->shopRepository->choose($user)->getQuery()->getResult();
        if(in_array($shop, $shopsOwned)) {
            unset($shopsOwned[array_search($shop, $shopsOwned, true)]);
        }
        $total = $this->orderRepository->findTotalForShop($shop);
        return $this->render('commercants/index.html.twig', [
            "shop" => $shop,
            "orders" => $orders,
            "products" => $products,
            "menu" => "home",
            "sidebar" => "home",
            "percentage" => $percentage,
            "total" => $total["sum"],
            'shopsOwned' => $shopsOwned
        ]);
    }
}