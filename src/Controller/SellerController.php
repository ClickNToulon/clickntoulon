<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Shop;
use App\Form\CategoryType;
use App\Form\ChooseShop;
use App\Form\OrderStatusCancel;
use App\Form\OrderStatusConfirm;
use App\Form\DeleteCategory;
use App\Form\EditProduct;
use App\Form\OrderStatusPickup;
use App\Form\OrderStatusPrepared;
use App\Form\OrderStatusReady;
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
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;

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
        $form_confirm = $this->createForm(OrderStatusConfirm::class, null);
        $form_confirm->handleRequest($request);
        if($form_confirm->isSubmitted() && $form_confirm->isValid()) {
            $data = $request->request->all('order_status_confirm');
            $order = $orderRepository->find($data['id']);
            $order->setTimeBegin(new DateTime($data['begin']))
                ->setTimeEnd(new DateTime($data['end']))
                ->setDay(new DateTime($data['date']))
                ->setStatus(1);
            $this->em->persist($order);
            $this->em->flush();
            $title = "Votre commande numéro " . $data['id'] . " a été acceptée par le commerçant";
            $options = [];
            array_push($options, $user->getUsername(), $data['id'], $data['date'], $data['begin'], $data['end']);
            if(isset($data['message']) && $data['message'] !== null) {
                array_push($options, $data['message']);
            }
            (new MailerController)->send($this->mailer, $user->getEmail(), $title, $options, 'orderaccept');
        }
        $form_prepared = $this->createForm(OrderStatusPrepared::class, null);
        $form_prepared->handleRequest($request);
        if($form_prepared->isSubmitted() && $form_prepared->isValid()) {
            $data = $request->get('id');
            $order = $orderRepository->find($data);
            $order->setStatus(2);
            $this->em->persist($order);
            $this->em->flush();
        }
        $form_ready = $this->createForm(OrderStatusReady::class, null);
        $form_ready->handleRequest($request);
        if($form_ready->isSubmitted() && $form_ready->isValid()) {
            $data = $request->get('id');
            $order = $orderRepository->find($data);
            $order->setStatus(3);
            $this->em->persist($order);
            $this->em->flush();
            $title = "Votre commande numéro " . $data['id'] . " est prête chez le commerçant";
            $options = [];
            array_push($options, $user->getUsername(), $data['id'], $data['date'], $data['begin'], $data['end']);
            (new MailerController)->send($this->mailer, $user->getEmail(), $title, $options, 'orderready');
        }
        $form_pickup = $this->createForm(OrderStatusPickup::class, null);
        $form_pickup->handleRequest($request);
        if($form_pickup->isSubmitted() && $form_pickup->isValid()) {
            $data = $request->get('id');
            $order = $orderRepository->find($data);
            $order->setStatus(4);
            $this->em->persist($order);
            $this->em->flush();
        }
        $form_cancel = $this->createForm(OrderStatusCancel::class, null);
        $form_cancel->handleRequest($request);
        if($form_cancel->isSubmitted() && $form_cancel->isValid()) {
            $data = $request->request->all('order_status_cancel');
            $order = $orderRepository->find($data['id']);
            $order->setStatus(5);
            $this->em->persist($order);
            $this->em->flush();
            $title = "Votre commande numéro " . $data['id'] . " a été annulée par le commerçant";
            $options = [];
            array_push($options, $user->getUsername(), $data['id']);
            if(isset($data['message']) && $data['message'] !== null) {
                array_push($options, $data['message']);
            }
            (new MailerController)->send($this->mailer, $user->getEmail(), $title, $options, 'orderrefuse');
        }
        $orders = $orderRepository->getAllShop($shop->getId());
        $orders_buyers = [];
        foreach ($orders as $key => $value) {
            $orders_buyers[] = $userRepository->find($value->getBuyerId());
        }
        $total_orders = count($orders);
        return $this->render('seller/index.html.twig', [
            'user' => $user,
            'shop' => $shop,
            'orders' => $orders,
            'orders_buyers' => $orders_buyers,
            'total_orders' => $total_orders,
            'form_confirm' => $form_confirm->createView(),
            'form_prepared' => $form_prepared->createView(),
            'form_ready' => $form_ready->createView(),
            'form_pickup' => $form_pickup->createView(),
            'form_cancel' => $form_cancel->createView()
        ]);
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
     * @return Response
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
        $form = $this->createForm(ProductType::class, $product);
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
            dump($request->request);
            $category_id = $request->request->all('category_id');
            dump($category_id);
            foreach ($category_id as $key => $value) {
                $category_id = $category_id[$key];
            }
            $category = $categoryRepository->find($category_id);
            $this->em->remove($category);
            $this->em->flush();
        }

        $categories = $categoryRepository->findAllByShop($shop);
        dump($categories);
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
     * @param Product $product
     * @param Shop $shop
     * @param Request $request
     * @return Response
     */
    public function editProduct(Product $product, Shop $shop, Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProduct::class, $product, ['id' => $shop]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            dump($data);
            //$this->em->persist($product);
            //$this->em->flush();
        }

        return $this->render("seller/edit_product.html.twig", [
            'user' => $user,
            'shop' => $shop,
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}