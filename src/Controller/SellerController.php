<?php

namespace App\Controller;

use App\Entity\OpeningHours;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\ChooseShopForm;
use App\Form\ProductForm;
use App\Form\ShopDeleteForm;
use App\Form\ShopForm;
use App\Form\ShopUpdateForm;
use App\Repository\OpeningHoursRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\ShopRepository;
use App\Repository\TagRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides all routes for the seller dashboard
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[Route(path: "/ma-boutique", name: "seller_")]
class SellerController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository,
        private OrderRepository $orderRepository,
        private MailerInterface $mailer,
        private PaymentRepository $paymentRepository,
        private TagRepository$tagRepository,
        private ProductTypeRepository $productTypeRepository,
        private OpeningHoursRepository $openingHoursRepository,
        private PaginatorInterface $paginator
    ){}

    #[Route(path: "/{id}", name: "index", requirements: ["id" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function index(Shop $shop, Request $request): Response
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            $shopsUser = $this->shopRepository->findAllByUser($user->getId());
            if (!in_array($shop, $shopsUser)) {
                return new Response($this->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
            }
        } else {
            return new Response($this->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
        }
        $orders = $this->orderRepository->getAllShop($shop)->getResult();
        $total_orders = count($orders);
        $orders_buyers = [];
        $quantities = [];
        $products = [];
        foreach ($orders as $order) {
            $orders_buyers[$order->getId()] = $order->getBuyer();
            $orders_quantities[$order->getId()] = $order->getQuantity();
            foreach ($orders_quantities[$order->getId()] as $oq) {
                $quantities[$order->getId()][] = $oq;
            }
            $orders_products[$order->getId()] = $order->getProducts();
            foreach ($orders_products[$order->getId()] as $op) {
                $products[$order->getId()][] = $this->productRepository->find($op->getId());
            }
        }
        $pagination_orders = $this->paginator->paginate(
            $this->orderRepository->getAllShop($shop),
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('seller/index.html.twig', [
            'user' => $user,
            'shop' => $shop,
            'orders' => $pagination_orders,
            'orders_buyers' => $orders_buyers,
            'total_orders' => $total_orders,
            'quantities' => $quantities,
            'products' => $products
        ]);
    }

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
        if (isset($order_infos['message']) && $order_infos['message'] !== null) {
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
        $order->setStatus(5);
        $order_user = $order->getBuyer();
        $this->em->persist($order);
        $this->em->flush();
        $title = "Votre commande numéro " . $order->getOrderNumber() . " a été annulée par le commerçant";
        $options = [];
        array_push($options, $order_user->getName(), $order->getOrderNumber());
        if (isset($order_infos['message']) && $order_infos['message'] !== null) {
            $options[] = $order_infos['message'];
        }
        (new MailerController)->send($this->mailer, $order_user->getEmail(), $title, $options, 'orderrefuse');
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    #[Route(path: "/choisir", name: "choose")]
    #[IsGranted("ROLE_MERCHANT")]
    public function choose(Request $request): Response
    {
        $user = $this->getUser();
        $shops = $this->shopRepository->findAllByUser($user);
        $form = $this->createForm(ChooseShopForm::class, null, ['user' => $user]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('select')->getViewData();
            $shop = $this->shopRepository->find($data);
            return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
        }
        return $this->render('seller/choose.html.twig', [
            'shops' => $shops,
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route(path: "/creation", name: "create")]
    #[IsGranted("ROLE_USER")]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        $shop = new Shop();
        $form = $this->createForm(ShopForm::class, $shop);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = sprintf("%s-%s-%s.%s", $shop->getName(), $shop->getPostalCode(), $shop->getCity(), $image->guessExtension());
                try {
                    $image->move(
                        $this->getParameter('uploads/shops'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $shop->setImage($newFilename);
            }
            $default_payment = $this->paymentRepository->find(1);
            [$shop, $response] = $this->setShopFromInfos($shop, $user, $default_payment, $form->get('tag')->getData());
            if($response) {
                return $response;
            }
            $this->em->persist($shop);
            $this->em->flush();
            $d = 1;
            for ($k = 0; $k < 28; $k += 4) {
                $day = $this->createOpeningHours($d, $shop);
                $day2 = $this->createOpeningHours($d, $shop);
                $this->em->flush();
                $shop->addOpeningHour($day2);
                $shop->addOpeningHour($day);
                $this->em->flush();
                $d = $d + 1;
            }
            $this->addFlash('success', 'Votre boutique a bien été créée');
        }
        return $this->render('seller/create.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: "/{id}/modifier", name: "edit", requirements: ["id" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function edit(Shop $shop, Request $request): Response
    {
        $user = $this->getUser();
        $form_update = $this->createForm(ShopUpdateForm::class, $shop);
        $payments = $this->paymentRepository->findAll();
        $form_update->handleRequest($request);
        $form_delete = $this->createForm(ShopDeleteForm::class, $shop);
        $form_delete->handleRequest($request);
        if ($form_update->isSubmitted() && $form_update->isValid()) {
            $payments_add = $request->request->all('payment');
            $payments_shop = $shop->getPayments();
            $payments_shop_array = [];
            foreach ($payments_shop as $v) {
                $payments_shop_array[] = $v->getId();
            }
            foreach ($payments_shop_array as $value) {
                if (in_array($value, $payments_add)) {
                    $payment_adding = $this->paymentRepository->find($value);
                    $shop->addPayment($payment_adding);
                } else {
                    $payment_removing = $this->paymentRepository->find($value);
                    $shop->removePayment($payment_removing);
                }
            }
            foreach ($payments_add as $value2) {
                if (!in_array($value2, $payments_shop_array)) {
                    $payment_adding = $this->paymentRepository->find($value2);
                    $shop->addPayment($payment_adding);
                }
            }
            $this->em->persist($shop);
            $this->em->flush();
            $this->addFlash('success', 'Votre boutique a bien été modifiée');
        }
        if ($form_delete->isSubmitted() && $form_delete->isValid()) {
            $this->em->remove($shop);
            $this->em->flush();
            return $this->redirectToRoute('seller_choose');
        }
        $openingHours = $this->openingHoursRepository->findBy(["shop" => $shop]);
        if ($request->getMethod() === "POST" && $request->request->get('day') !== null) {
            $data = $request->request->all();
            unset($data['day']);
            $k = 0;
            foreach ($data as $key => $value) {
                $data[$k] = $value;
                unset($data[$key]);
                $k = $k + 1;
            }
            $d = 1;
            foreach ($openingHours as $openingHour) {
                $this->em->remove($openingHour);
            }
            $this->em->flush();
            for ($i = 0; $i < count($data); $i+=4) {
                if ($data[$i] !== "" && $data[$i+1] !== "") {
                    [$day, $response] = $this->setOpeningHours($d, $shop, $data, $i);
                    if($response) {
                        return $response;
                    }
                    $this->em->persist($day);
                    $this->em->flush();
                    $shop->addOpeningHour($day);
                }
                if($data[$i+2] !== "" && $data[$i+3] !== "") {
                    [$day2, $response] = $this->setOpeningHours($d, $shop, $data, $i);
                    if($response) {
                        return $response;
                    }
                    $this->em->persist($day2);
                    $this->em->flush();
                    $shop->addOpeningHour($day2);
                }
                $d = $d + 1;
            }
            $this->em->flush();
            $this->addFlash('success', 'Les horaires ont bien été modifiés');
        }
        return $this->render('seller/edit.html.twig', [
            'shop' => $shop,
            'user' => $user,
            'form_update' => $form_update->createView(),
            'form_delete' => $form_delete->createView(),
            'payments' => $payments,
            'openingHours' => $openingHours
        ]);
    }

    #[Route(path: "/{id}/produits", name: "edit_products", requirements: ["id" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function products(Shop $shop, Request $request): Response
    {
        $user = $this->getUser();
        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $product->setShop($shop);
            $this->checkUnitDiscountPrice($product);
            [$product, $response] = $this->setProductImages($images, $product, $shop);
            if($response) {
                return $response;
            }
            $product->getType()->addProduct($product);
            $this->em->persist($product);
            $this->em->flush();
            $this->addFlash('success', 'Le produit a bien été créé');
        }
        $products = $this->paginator->paginate(
            $this->productRepository->findAllByShopQuery($shop),
            $request->query->getInt('page', 1),
            6
        );
        $total_products = count($products);
        return $this->render('seller/products.html.twig', [
            'user' => $user,
            'shop' => $shop,
            'products' => $products,
            'total_products' => $total_products,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: "/{id}/produits/{product}/modifier", name: "edit_product", requirements: ["id" => "[0-9\-]*", "product" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function editProduct(Request $request): Response
    {
        $user = $this->getUser();
        $product = $this->productRepository->find($request->attributes->get('product'));
        $shop = $this->shopRepository->find($request->attributes->get('id'));
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $product->setName($product->getName())
                ->setDescription($product->getDescription())
                ->setUnitPrice($product->getUnitPrice())
                ->setType($form->get('type')->getData());
            $this->checkUnitDiscountPrice($product);
            [$product, $response] = $this->setProductImages($images, $product, $shop);
            if($response) {
                return $response;
            }
            $product->getType()->addProduct($product);
            $this->em->persist($product);
            $this->em->flush();
            return $this->redirectToRoute('seller_edit_products', ['id' => $shop->getId()]);
        }
        return $this->render("seller/edit_product.html.twig", [
            'user' => $user,
            'shop' => $shop,
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: "/{id}/produits/{product}/supprimer", name: "delete_product", requirements: ["id" => "[0-9\-]*", "product" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function delete_product(Request $request): RedirectResponse
    {
        $product = $this->productRepository->find($request->attributes->get('product'));
        $shop = $this->shopRepository->find($request->attributes->get('id'));
        $this->em->remove($product);
        $this->em->flush();
        $this->addFlash('warning', 'Le produit a bien été supprimé');
        return $this->redirectToRoute('seller_edit_products', ["id" => $shop->getId()]);
    }

    private function setProductImages(array $images, Product $product, Shop $shop): array
    {
        $limit = count($images);
        for ($i = 0; $i < $limit; $i++) {
            if ($images[$i]) {
                $newFilename = sprintf("%s-%s-%s-%s.%s", $product->getName(), $shop->getId(), $shop->getName(), $i, $images[$i]->guessExtension());
                try {
                    $images[$i]->move(
                        $this->getParameter('uploads/products'),
                        $newFilename
                    );
                } catch (FileException) {
                    $response = new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR);
                    return [null, $response];
                }
                $product_images = $product->getImages();
                $product_images[] = $newFilename;
                $product->setImages($product_images);
            }
        }
        return [$product, null];
    }

    private function checkUnitDiscountPrice(Product $product): void
    {
        if ($product->getUnitPriceDiscount() == null) {
            $product->setUnitPriceDiscount(null);
        } else {
            $product->setUnitPriceDiscount($product->getUnitPriceDiscount());
        }
    }

    private function setOpeningHours(int $d, Shop $shop, array $data, int $i): array
    {
        $day = new OpeningHours();
        try {
            $day->setDay($d)
                ->setShop($shop)
                ->setStart(new DateTime($data[$i], new DateTimeZone("Europe/Paris")))
                ->setEnd(new DateTime($data[$i+1], new DateTimeZone("Europe/Paris")));
        } catch (Exception) {
            $response = new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR);
            return [null, $response];
        }

        return [$day, null];
    }

    private function createOpeningHours(int $d, Shop $shop): OpeningHours
    {
        $day = new OpeningHours();
        $day->setDay($d)
            ->setShop($shop)
            ->setStart(null)
            ->setEnd(null);
        $this->em->persist($day);
        return $day;
    }

    private function setShopFromInfos(Shop $shop, User|UserInterface $user, Payment $payment, Tag $tag): array
    {
        try {
            $shop->setOwner($user)
                ->setCreatedAt(new DateTime('now', new DateTimeZone("Europe/Paris")))
                ->setUpdatedAt(new DateTime('now', new DateTimeZone("Europe/Paris")))
                ->setStatus(0)
                ->setIsBanned(0)
                ->setIsVerified(0)
                ->addPayment($payment)
                ->setSlug($shop->getSlug())
                ->setTag($tag);
        } catch (Exception) {
            $response = new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR);
            return [null, $response];
        }
        return [$shop, null];
    }
}