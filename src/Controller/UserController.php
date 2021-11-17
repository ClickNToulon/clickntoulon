<?php

namespace App\Controller;

use App\Form\DeleteUserForm;
use App\Form\UpdatePasswordForm;
use App\Form\UpdateUserForm;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    private RequestStack $requestStack;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, RequestStack $requestStack) {
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/profil/modifier", name="user_edit")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        // Traitement du mot de passe
        [$formPassword, $response] = $this->createFormPassword($request);
        if ($response) {
            return $response;
        }

        [$formInfo, $responseInfo] = $this->createFormInfos($request);
        if ($responseInfo) {
            return $responseInfo;
        }

        [$formDelete, $responseDelete] = $this->createFormDelete($request);
        if ($responseDelete) {
            return $responseDelete;
        }

        return $this->render('user/edit.html.twig', [
            'form_password' => $formPassword->createView(),
            'form_profile' => $formInfo->createView(),
            'form_delete' => $formDelete->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/profil/mes-commandes", name="user_orders")
     * @IsGranted("ROLE_USER")
     * @param OrderRepository $orderRepository
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function orders(OrderRepository $orderRepository, ShopRepository $shopRepository, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->findLast4ByUser($user);
        $products_id = [];
        $products = [];
        foreach ($orders as $key => $value) {
            $products_id[$value->getId()] = $value->getProducts();
            foreach ($products_id as $key2 => $value2) {
                if(str_contains($value2, ',') == true) {
                    $value3 = [];
                    $value3 = explode(',', $value2);
                    $products[$value->getId()] = [];
                    foreach ($value3 as $key4 => $value4) {
                        $products[$value->getId()][] = $productRepository->find($value4);
                    }
                } else {
                    $products[$value->getId()] = $productRepository->find($value2);
                }
            }
        }
        $shops = $shopRepository->findAll();
        return $this->render('user/orders.html.twig', [
            'user' => $user,
            'orders' => $orders,
            'shops' => $shops,
            'products' => $products
        ]);
    }


    /**
     * @Route("/profil/mes-commandes/{number}", name="user_order", requirements={"id": "[0-9\-]*"})
     * @param OrderRepository $orderRepository
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     */
    public function order(OrderRepository $orderRepository, ShopRepository $shopRepository, ProductRepository $productRepository, Request $request): Response
    {
        $user = $this->getUser();
        $order = $orderRepository->findOneBy(['number' => $request->attributes->get('number')]);
        $products = [];
        $products_id = [];
        $order_products = $order->getProducts();
        foreach ($order_products as $order_product) {
            $products_id[] = $order_product->getId();
        }
        foreach ($products_id as $key => $value) {
            $products[] = $productRepository->find($value);
        }
        $products_quantity = $order->getQuantity();
        $shop = $shopRepository->find($order->getShop()->getId());
        return $this->render('user/order.html.twig', [
            'user' => $user,
            'order' => $order,
            'shop' => $shop,
            'products' => $products,
            'quantities' => $products_quantity
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
     * @return array
     */
    public function createFormDelete(Request $request): array
    {
        $user = $this->getUser();
        $form = $this->createForm(DeleteUserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $this->requestStack->getSession();
            $session = new Session();
            $session->invalidate();
            $this->em->remove($user);
            $this->em->flush();
            return [$form, $this->redirectToRoute('home')];
        }

        return [$form, null];
    }
}