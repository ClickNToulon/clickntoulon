<?php

namespace App\Controller;

use App\Form\DeleteUserForm;
use App\Form\UpdatePasswordForm;
use App\Form\UpdateUserForm;
use App\Repository\OrderRepository;
use App\Repository\ShopRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route(path: "/profil", name: "user_")]
class UserController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em,
        private OrderRepository $orderRepository,
        private ShopRepository $shopRepository
    ){}

    /**
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     * @throws Exception
     */
    #[
        Route(path: "/modifier", name: "edit"),
        IsGranted("ROLE_USER")
    ]
    public function edit(Request $request, TokenStorageInterface $tokenStorage): Response
    {
        // Traitement du mot de passe
        [$formPassword, $response] = $this->createFormPassword($request);
        if ($response) return $response;

        [$formInfo, $responseInfo] = $this->createFormInfos($request);
        if ($responseInfo) return $responseInfo;

        [$formDelete, $responseDelete] = $this->createFormDelete($request, $tokenStorage);
        if ($responseDelete) return $responseDelete;

        $user = $this->getUser();
        if ($user == null) {
           return $this->redirectToRoute('home');
        }

        return $this->render('user/edit.html.twig', [
            'form_password' => $formPassword->createView(),
            'form_profile' => $formInfo->createView(),
            'form_delete' => $formDelete->createView(),
            'user' => $user
        ]);
    }

    /**
     * @return Response
     */
    #[
        Route(path: "/mes-commandes", name: "orders"),
        IsGranted("ROLE_USER")
    ]
    public function orders(): Response
    {
        $user = $this->getUser();
        $orders = $this->orderRepository->findLast4ByUser($user);
        $products = [];
        $quantities = [];
        foreach ($orders as $order) {
            $order_products = $order->getProducts();
            $order_quantities = $order->getQuantity();
            foreach ($order_quantities as $quantity) {
                $quantities[$order->getId()][] = $quantity;
            }
            foreach ($order_products as $op) {
                $products[$order->getId()][] = $op;
            }
        }
        $shops = $this->shopRepository->findAll();
        return $this->render('user/orders.html.twig', [
            'user' => $user,
            'orders' => $orders,
            'shops' => $shops,
            'products' => $products,
            'quantities' => $quantities
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route(path: "/mes-commandes/{number}", name: "order", requirements: ["id" => "[0-9\-]*"])]
    public function order(Request $request): Response
    {
        $user = $this->getUser();
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
        $shop = $this->shopRepository->find($order->getShop()->getId());
        return $this->render('user/order.html.twig', [
            'user' => $user,
            'order' => $order,
            'shop' => $shop,
            'products' => $products,
            'quantities' => $quantities
        ]);
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    private function createFormPassword(Request $request): array
    {
        $form = $this->createForm(UpdatePasswordForm::class);
        $user = $this->getUser();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('password')->getData();
            $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
            $user->setPassword($this->passwordHasher->hashPassword($user, $data))
                ->setUpdatedAt(new DateTime('now', $dateTimeZoneFrance));
            $this->em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');

            return [$form, $this->redirectToRoute('user_edit')];
        }
        return [$form, null];
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    private function createFormInfos(Request $request): array
    {
        $user = $this->getUser();
        $form = $this->createForm(UpdateUserForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
            $user->setUpdatedAt(new DateTime('now', $dateTimeZoneFrance));
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');
            return [$form, $this->redirectToRoute('user_edit')];
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Problème rencontré');
        }
        return [$form, null];
    }

    /**
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @return array
     */
    public function createFormDelete(Request $request, TokenStorageInterface $tokenStorage): array
    {
        $user = $this->getUser();
        $form = $this->createForm(DeleteUserForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            $tokenStorage->setToken();
            $session->invalidate();
            $this->em->remove($user);
            $this->em->flush();
            return [$form, $this->redirectToRoute('home')];
        }
        return [$form, null];
    }
}