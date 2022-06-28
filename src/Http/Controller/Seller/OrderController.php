<?php

namespace App\Http\Controller\Seller;

use App\Domain\Buyer\OrderRepository;
use App\Domain\Shop\Shop;
use App\Http\Controller\MailerController;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/ma-boutique", name: "seller_")]
class OrderController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OrderRepository        $orderRepository,
        private readonly MailerInterface        $mailer,
    ){}

    #[Route(path: "/{id}/commandes/{order}/confirmer", name: "orders_confirm", requirements: ["id" => "[0-9\-]*", "order" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function confirm_order(Shop $shop, Request $request): Response
    {
        $order = $this->orderRepository->find($request->attributes->get('order'));
        $order_infos = $request->request->all('order_confirm');
        try {
            $data_date = new DateTime($order_infos['day'], new DateTimeZone("Europe/Paris"));
            if($data_date != $order->getDay()) {
                $order->setDay(new DateTime($order_infos['day'], new DateTimeZone("Europe/Paris")));
            }
            $order->setStatus(1);
        } catch (Exception) {
            return new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $order_user = $order->getBuyer();
        $this->em->persist($order);
        $this->em->flush();
        $title = "Votre commande numéro " . $order->getOrderNumber() . " a été acceptée par le commerçant";
        $options = [];
        array_push($options, $order_user->getName(), $order->getOrderNumber(), $order_infos['day']);
        if (isset($order_infos['message'])) {
            $options[] = $order_infos['message'];
        }
        (new MailerController)->send($this->mailer, $order_user->getEmail(), $title, $options, 'orderaccept');
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    #[Route(path: "/{id}/commandes/{order}/prete", name: "orders_ready", requirements: ["id" => "[0-9\-]*", "order" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function order_ready(Shop $shop, Request $request): Response
    {
        $order = $this->orderRepository->find($request->attributes->get('order'));
        $order_infos = $request->request->all('order_ready');
        $order->setStatus(2);
        $order_user = $order->getBuyer();
        $this->em->persist($order);
        $this->em->flush();
        $title = "Votre commande numéro " . $order->getOrderNumber() . " est prête chez le commerçant";
        $options = [];
        array_push($options, $order_user->getName(), $order->getOrderNumber(), $order_infos['day']);
        (new MailerController)->send($this->mailer, $order_user->getEmail(), $title, $options, 'orderready');
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    #[Route(path: "/{id}/commandes/{order}/recuperee", name: "orders_pickup", requirements: ["id" => "[0-9\-]*", "order" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function order_pickup(Shop $shop, Request $request): Response
    {
        $order = $this->orderRepository->find($request->attributes->get('order'));
        $order->setStatus(3);
        $this->em->persist($order);
        $this->em->flush();
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    #[Route(path: "/{id}/commandes/{order}/annulation", name: "orders_cancel", requirements: ["id" => "[0-9\-]*", "order" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function order_cancel(Shop $shop, Request $request): Response
    {
        $order = $this->orderRepository->find($request->attributes->get('order'));
        $order_infos = $request->request->all('order_cancel');
        $order->setStatus(4);
        $order_user = $order->getBuyer();
        $this->em->persist($order);
        $this->em->flush();
        $title = "Votre commande numéro " . $order->getOrderNumber() . " a été annulée par le commerçant";
        $options = [];
        array_push($options, $order_user->getName(), $order->getOrderNumber());
        if (isset($order_infos['message'])) {
            $options[] = $order_infos['message'];
        }
        (new MailerController)->send($this->mailer, $order_user->getEmail(), $title, $options, 'orderrefuse');
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    #[Route(path: "/{id}/commandes/{number}/details", name: "orders_details", requirements: ["id" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function details(Shop $shop, Request $request): Response
    {
        $order = $this->orderRepository->findOneBy(['orderNumber' => $request->attributes->get('number')]);
        $products = [];
        $order_products = $order->getProducts();
        foreach ($order_products as $op) {
            $products[] = $op;
        }
        $order_quantities = $order->getQuantity();
        $quantities = [];
        foreach ($order_quantities as $quantity) {
            $quantities[] = $quantity;
        }
        return $this->render('seller/order_details.html.twig', [
            'order' => $order,
            'shop' => $shop,
            'products' => $products,
            'quantities' => $quantities
        ]);
    }
}