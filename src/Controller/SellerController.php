<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Shop;
use App\Form\CategoryType;
use App\Form\ChooseShop;
use App\Form\DeleteCategory;
use App\Form\ProductType;
use App\Form\ShopDeleteForm;
use App\Form\ShopTimeTableUpdate;
use App\Form\ShopType;
use App\Form\ShopUpdateForm;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use App\Repository\TimeTableRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Error\LoaderError;

class SellerController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    /**
     * @var ShopRepository
     */
    private ShopRepository $shopRepository;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $em, ShopRepository $shopRepository, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @Route("/ma-boutique/{id}", name="seller_index", requirements={"id": "[0-9\-]*"})
     * @IsGranted("ROLE_MERCHANT")
     * @param Shop $shop
     * @param OrderRepository $orderRepository
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Shop $shop, OrderRepository $orderRepository, UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $shopsUser = $this->shopRepository->findAllByUser($user->getId());
        if (!in_array($shop, $shopsUser)) {
            return new Response($this->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
        }
        $orders = $orderRepository->getAllShop($shop->getId());
        $orders_buyers = [];
        $quantities = [];
        $products_id = [];
        foreach ($orders as $key => $value) {
            $orders_buyers[$value->getId()] = $userRepository->find($value->getBuyerId());
            $quantities[$value->getId()] = $value->getQuantity();
            $products_id[$value->getId()] = $value->getProductsId();
        }
        $total_orders = count($orders);
        return $this->render('seller/index.html.twig', [
            'user' => $user,
            'shop' => $shop,
            'orders' => $orders,
            'orders_buyers' => $orders_buyers,
            'total_orders' => $total_orders,
            'quantities' => $quantities,
            'products_id' => $products_id,
        ]);
    }

    /**
     * @Route("/ma-boutique/{id}/commandes/{order}/confirmer", name="seller_orders_confirm", requirements={"id": "[0-9\-]*", "order": "[0-9\-]*"})
     * @IsGranted("ROLE_MERCHANT")
     * @param Shop $shop
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function confirm_order(Shop $shop, OrderRepository $orderRepository, Request $request): Response
    {
        $user = $this->getUser();
        $order = $orderRepository->find($request->attributes->get('order'));
        $order_infos = $request->request->all('order_confirm');
        $order->setTimeBegin(new DateTime($order_infos['begin']))
            ->setTimeEnd(new DateTime($order_infos['end']))
            ->setDay(new DateTime($order_infos['day']))
            ->setStatus(1);
        $this->em->persist($order);
        $this->em->flush();
        $title = "Votre commande numéro " . $order_infos['id'] . " a été acceptée par le commerçant";
        $options = [];
        array_push($options, $user->getFullName(), $order_infos['id'], $order_infos['day'], $order_infos['begin'], $order_infos['end']);
        if(isset($order_infos['message']) && $order_infos['message'] !== null) {
            array_push($options, $order_infos['message']);
        }
        (new MailerController)->send($this->mailer, $user->getEmail(), $title, $options, 'orderaccept');
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    /**
     * @Route("/ma-boutique/{id}/commandes/{order}/preparation", name="seller_orders_prepared", requirements={"id": "[0-9\-]*", "order": "[0-9\-]*"})
     * @IsGranted("ROLE_MERCHANT")
     * @param Shop $shop
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @return Response
     */
    public function prepared_order(Shop $shop, OrderRepository $orderRepository, Request $request): Response
    {
        $order = $orderRepository->find($request->attributes->get('order'));
        $order->setStatus(2);
        $this->em->persist($order);
        $this->em->flush();
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    /**
     * @Route("/ma-boutique/{id}/commandes/{order}/prête", name="seller_orders_ready", requirements={"id": "[0-9\-]*", "order": "[0-9\-]*"})
     * @IsGranted("ROLE_MERCHANT")
     * @param Shop $shop
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @return Response
     * @throws LoaderError
     */
    public function order_ready(Shop $shop, OrderRepository $orderRepository, Request $request): Response
    {
        $user = $this->getUser();
        $order = $orderRepository->find($request->attributes->get('order'));
        $order_infos = $request->request->all('order_ready');
        $order->setStatus(3);
        $this->em->persist($order);
        $this->em->flush();
        $title = "Votre commande numéro " . $order_infos['id'] . " est prête chez le commerçant";
        $options = [];
        array_push($options, $user->getFullName(), $order_infos['id'], $order_infos['day'], $order_infos['begin'], $order_infos['end']);
        (new MailerController)->send($this->mailer, $user->getEmail(), $title, $options, 'orderready');
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    /**
     * @Route("/ma-boutique/{id}/commandes/{order}/recuperée", name="seller_orders_pickup", requirements={"id": "[0-9\-]*", "order": "[0-9\-]*"})
     * @IsGranted("ROLE_MERCHANT")
     * @param Shop $shop
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @return Response
     */
    public function order_pickup(Shop $shop, OrderRepository $orderRepository, Request $request): Response
    {
        $order = $orderRepository->find($request->attributes->get('order'));
        $order->setStatus(4);
        $this->em->persist($order);
        $this->em->flush();
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    /**
     * @Route("/ma-boutique/{id}/commandes/{order}/annulation", name="seller_orders_cancel", requirements={"id": "[0-9\-]*", "order": "[0-9\-]*"})
     * @IsGranted("ROLE_MERCHANT")
     * @param Shop $shop
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @return Response
     * @throws LoaderError
     */
    public function order_cancel(Shop $shop, OrderRepository $orderRepository, Request $request): Response
    {
        $user = $this->getUser();
        $order = $orderRepository->find($request->attributes->get('order'));
        $order_infos = $request->request->all('order_cancel');
        $order->setStatus(6);
        $this->em->persist($order);
        $this->em->flush();
        $title = "Votre commande numéro " . $order_infos['id'] . " a été annulée par le commerçant";
        $options = [];
        array_push($options, $user->getFullName(), $order_infos['id']);
        if(isset($order_infos['message']) && $order_infos['message'] !== null) {
            array_push($options, $order_infos['message']);
        }
        (new MailerController)->send($this->mailer, $user->getEmail(), $title, $options, 'orderrefuse');
        return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
    }

    /**
     * @Route("/ma-boutique/choisir", name="seller_choose")
     * @IsGranted("ROLE_MERCHANT")
     * @param Request $request
     * @return Response
     */
    public function choose(Request $request): Response
    {
        $user = $this->getUser();
        $shops = $this->shopRepository->findAllByUser($user->getId());
        $form = $this->createForm(ChooseShop::class, null, ['id' => $user->getId()]);
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

    /**
     * @Route("/ma-boutique/creation", name="seller_create")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param PaymentRepository $paymentRepository
     * @return Response
     * @throws Exception
     */
    public function create(Request $request, SluggerInterface $slugger, PaymentRepository $paymentRepository): Response
    {
        $user = $this->getUser();
        $shop = new Shop();
        $form = $this->createForm(ShopType::class, $shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cover = $form->get('cover')->getData();
            if ($cover) {
                $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename, '-', 'fr');
                $newFilename = sprintf("%s-%s-%s.%s", $safeFilename, $shop->getName(), $user->getId(), $cover->guessExtension());

                try {
                    $cover->move(
                        $this->getParameter('uploads/shops'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $shop->setCover($newFilename);
            }
            $default_payment = $paymentRepository->find(1);
            $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
            $shop
                ->setOwnerId($user->getId())
                ->setCreatedAt(new DateTime('now', $dateTimeZoneFrance))
                ->setUpdatedAt(new DateTime('now', $dateTimeZoneFrance))
                ->setStatus(0)
                ->setBanned(0)
                ->setIsVerified(0)
                ->addPayment($default_payment)
                ->setSlug($shop->getSlug());
            $this->em->persist($shop);
            $this->em->flush();
            $this->addFlash('success', 'Votre boutique a bien été créée');
        }
        return $this->render('seller/create.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ma-boutique/{id}/modifier", name="seller_edit", requirements={"id": "[0-9\-]*"})
     * @param Shop $shop
     * @param Request $request
     * @param PaymentRepository $paymentRepository
     * @param TimeTableRepository $timeTableRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(Shop $shop, Request $request, PaymentRepository $paymentRepository, TimeTableRepository $timeTableRepository): Response
    {
        $user = $this->getUser();
        $form_update = $this->createForm(ShopUpdateForm::class, $shop);
        $payments = $paymentRepository->findAll();
        $form_update->handleRequest($request);

        $form_delete = $this->createForm(ShopDeleteForm::class, $shop);
        $form_delete->handleRequest($request);

        $shop_timetable = $timeTableRepository->findById($shop);
        $form_timetable = $this->createForm(ShopTimeTableUpdate::class, $shop_timetable);
        $form_timetable->handleRequest($request);

        if ($form_update->isSubmitted() && $form_update->isValid()) {
            $payments_add = $request->request->all('payment');
            $payments_shop = $shop->getPayments();
            $payments_shop_array = [];
            foreach ($payments_shop as $k => $v) {
                $payments_shop_array[] = $v->getId();
            }
            foreach($payments_shop_array as $key => $value) {
                if(in_array($value, $payments_add)) {
                    $payment_adding = $paymentRepository->find($value);
                    $shop->addPayment($payment_adding);
                } else {
                    $payment_removing = $paymentRepository->find($value);
                    $shop->removePayment($payment_removing);
                }
            }
            foreach ($payments_add as $key2 => $value2) {
                if(!in_array($value2, $payments_shop_array)) {
                    $payment_adding = $paymentRepository->find($value2);
                    $shop->addPayment($payment_adding);
                }
            }

            $this->em->persist($shop);
            $this->em->flush();
            $this->addFlash('success', 'Votre boutique a bien été modifiée');
        }

        if($form_timetable->isSubmitted() && $form_timetable->isValid()) {
            $data = $form_timetable->getData();
            $this->em->persist($data);
            $this->em->flush();
        }

        if ($form_delete->isSubmitted() && $form_delete->isValid()) {
            $this->em->remove($shop);
            $this->em->flush();
            return $this->redirectToRoute('seller_choose', );
        }

        return $this->render('seller/edit.html.twig', [
            'shop' => $shop,
            'user' => $user,
            'form_update' => $form_update->createView(),
            'form_delete' => $form_delete->createView(),
            'form_timetable' => $form_timetable->createView(),
            'payments' => $payments
        ]);

    }


    /**
     * @Route("/ma-boutique/{id}/produits", name="seller_edit_products", requirements={"id": "[0-9\-]*"})
     * @param Shop $shop
     * @param ProductRepository $productRepository
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function products(Shop $shop, ProductRepository $productRepository, Request $request, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, ['id' => $shop->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $product->setName($product->getName())
                ->setStatus(0)
                ->setDescription($product->getDescription())
                ->setShopId($shop->getId())
                ->setPrice($product->getPrice());
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename, '-', 'fr');
                $newFilename = sprintf("%s-%s-%s.%s", $safeFilename, $product->getName(), $user->getId(), $image->guessExtension());

                try {
                    $image->move(
                        $this->getParameter('uploads/products'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $product->setImage($newFilename);
            }
            $this->em->persist($product);
            $this->em->flush();
        }
        $products = $productRepository->findAllByShop($shop);
        $total_products = count($products);
        return $this->render('seller/products.html.twig', [
            'user' => $user,
            'shop' => $shop,
            'products' => $products,
            'total_products' => $total_products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ma-boutique/{id}/categories", name="seller_categories", requirements={"id": "[0-9\-]*"})
     * @param Shop $shop
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @return Response
     */
    public function categories(Shop $shop, CategoryRepository $categoryRepository, Request $request): Response
    {
        $user = $this->getUser();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setShop($shop);
            $this->em->persist($category);
            $this->em->flush();
        }

        $form_delete = $this->createForm(DeleteCategory::class, $category);
        $form_delete->handleRequest($request);

        if($form_delete->isSubmitted() && $form_delete->isValid()) {
            $category_id = $request->request->all('category_id');
            foreach ($category_id as $key => $value) {
                $category_id = $category_id[$key];
            }
            $category = $categoryRepository->find($category_id);
            $this->em->remove($category);
            $this->em->flush();
        }

        $categories = $categoryRepository->findAllByShop($shop);
        $total_categories = count($categories);
        return $this->render("seller/categories.html.twig", [
            'user' => $user,
            'shop' => $shop,
            'categories' => $categories,
            'total_categories' => $total_categories,
            'form' => $form->createView(),
            'form_delete' => $form_delete->createView()
        ]);
    }


    /**
     * @Route("/ma-boutique/{id}/produits/{product}/modifier", name="seller_edit_product", requirements={"id": "[0-9\-]*", "product": "[0-9\-]*"})
     * @IsGranted("ROLE_MERCHANT")
     * @param ShopRepository $shopRepository
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param SluggerInterface $slugger
     * @return Response
     * @throws Exception
     */
    public function editProduct(ShopRepository $shopRepository, Request $request, ProductRepository $productRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $product = $productRepository->find($request->attributes->get('product'));
        $shop = $shopRepository->find($request->attributes->get('id'));
        $form_update_product = $this->createForm(ProductType::class, $product, ['id' => $shop->getId()]);
        $form_update_product->handleRequest($request);
        if($form_update_product->isSubmitted() && $form_update_product->isValid()) {
            $data = $request->request->all('product');
            $image = $form_update_product->get('image')->getData();
            $price = floatval(str_replace(",",".",$data['price']));
            $deal_price = floatval(str_replace(",",".",$data['deal_price']));
            $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
            $product->setName($data['name'])
                ->setDescription($data['description'])
                ->setPrice($price);
            if($deal_price == "") {
                $product->setDealPrice(null);
            } else {
                $product->setDealPrice($deal_price);
            }
            if($data['deal_start'] == "") {
                $product->setDealStart(null);
            } else {
                $product->setDealStart(new DateTime($data['deal_start']));
            }
            if($data['deal_end'] == "") {
                $product->setDealEnd(null);
            } else {
                $product->setDealEnd(new DateTime($data['deal_end']));
            }
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename, '-', 'fr');
                $newFilename = sprintf("%s-%s-%s.%s", $safeFilename, $data['name'], $user->getId(), $image->guessExtension());

                try {
                    $image->move(
                        $this->getParameter('uploads/products'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $product->setImage($newFilename);
            }
            $this->em->persist($product);
            $this->em->flush();
        }

        return $this->render("seller/edit_product.html.twig", [
            'user' => $user,
            'shop' => $shop,
            'product' => $product,
            'form' => $form_update_product->createView()
        ]);
    }
}