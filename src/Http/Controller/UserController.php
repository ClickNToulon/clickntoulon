<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Buyer\OrderRepository;
use App\Domain\Shop\ShopRepository;
use App\Http\Form\User\DeleteUserForm;
use App\Http\Form\User\UpdatePasswordForm;
use App\Http\Form\User\UpdateUserForm;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides the routes for the user account edition, the orders listing and unique order listing
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[Route(path: "/profil", name: "user_")]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface      $em,
        private readonly OrderRepository             $orderRepository,
        private readonly ShopRepository $shopRepository
    ){}

    #[Route(path: "/modifier", name: "edit")]
    #[IsGranted("ROLE_USER")]
    public function edit(Request $request, TokenStorageInterface $tokenStorage): Response
    {
        /** Traitement du mot de passe de l'utilisateur */
        [$formPassword, $response] = $this->createFormPassword($request);
        if ($response) return $response;

        /** Traitement des informations de l'utilisateur */
        [$formInfo, $responseInfo] = $this->createFormInfos($request);
        if ($responseInfo) return $responseInfo;

        /** Traitement de la suppression de l'utilisateur */
        [$formDelete, $responseDelete] = $this->createFormDelete($request, $tokenStorage);
        if ($responseDelete) return $responseDelete;

        /** @var User|UserInterface $user */
        $user = $this->getUser();
        if ($user == null) {
           return $this->redirectToRoute('home');
        }
        return $this->render('user/edit.html.twig', [
            'form_password' => $formPassword->createView(),
            'form_profile' => $formInfo->createView(),
            'form_delete' => $formDelete->createView(),
        ]);
    }

    #[Route(path: "/mes-commandes", name: "orders")]
    #[IsGranted("ROLE_USER")]
    public function orders(): Response
    {
        $user = $this->getUser();
        $orders = $this->orderRepository->findLast4ByUser($user);
        $shops = $this->shopRepository->findAll();
        return $this->render('user/orders.html.twig', [
            'orders' => $orders,
            'shops' => $shops
        ]);
    }

    #[Route(path: "/mes-commandes/{number}", name: "order", requirements: ["id" => "[0-9\-]*"])]
    public function order(Request $request): Response
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
        $shop = $this->shopRepository->find($order->getShop()->getId());
        return $this->render('user/order.html.twig', [
            'order' => $order,
            'shop' => $shop,
            'products' => $products,
            'quantities' => $quantities
        ]);
    }

    private function createFormPassword(Request $request): array
    {
        $form = $this->createForm(UpdatePasswordForm::class);
        /** @var User|UserInterface $user */
        $user = $this->getUser();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('password')->getData();
            $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
            try{
                $user
                    ->setPassword($this->passwordHasher->hashPassword($user, $data))
                    ->setUpdatedAt(new DateTime('now', $dateTimeZoneFrance));
            } catch (Exception) {
                return [$form, new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR)];
            }
            $this->em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');

            return [$form, $this->redirectToRoute('user_edit')];
        }
        return [$form, null];
    }

    private function createFormInfos(Request $request): array
    {
        /** @var User|UserInterface $user */
        $user = $this->getUser();
        $form = $this->createForm(UpdateUserForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setUpdatedAt(new DateTime('now', new DateTimeZone("Europe/Paris")));
            } catch (Exception) {
                return [$form, new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR)];
            }
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');
            return [$form, $this->redirectToRoute('user_edit')];
        } else if($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Problème rencontré');
        }
        return [$form, null];
    }

    public function createFormDelete(Request $request, TokenStorageInterface $tokenStorage): array
    {
        /** @var User|UserInterface $user */
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