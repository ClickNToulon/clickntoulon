<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\OpeningHours;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\User;
use App\Form\CategoryForm;
use App\Form\ChooseShopForm;
use App\Form\DeleteCategoryForm;
use App\Form\ProductForm;
use App\Form\ShopDeleteForm;
use App\Form\ShopForm;
use App\Form\ShopUpdateForm;
use App\Repository\CategoryRepository;
use App\Repository\OpeningHoursRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\ShopRepository;;

use App\Repository\TagRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     */
    public function index(Shop $shop, OrderRepository $orderRepository, ProductRepository $productRepository, Request $request): Response
    {
        $user = $this->getUser();
        if($user instanceof User) {
            $shopsUser = $this->shopRepository->findAllByUser($user->getId());
            if (!in_array($shop, $shopsUser)) {
                return new Response($this->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
            }
        } else {
            return new Response($this->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
        }
        $orders = $orderRepository->getAllShop($shop);
        $orders_buyers = [];
        $quantities = [];
        $products = [];
        foreach ($orders as $key => $value) {
            $orders_buyers[$value->getId()] = $value->getBuyer();
            $orders_quantities[$value->getId()] = $value->getQuantity();
            foreach ($orders_quantities[$value->getId()] as $oq) {
                $quantities[$value->getId()][] = $oq;
            }
            $orders_products[$value->getId()] = $value->getProducts();
            foreach ($orders_products[$value->getId()] as $op) {
                    $products[$value->getId()][] = $productRepository->find($op->getId());
            }
        }
        $total_orders = count($orders);
        dump($products);
        dump($quantities);
        return $this->render('seller/index.html.twig', [
            'user' => $user,
            'shop' => $shop,
            'orders' => $orders,
            'orders_buyers' => $orders_buyers,
            'total_orders' => $total_orders,
            'quantities' => $quantities,
            'products' => $products
        ]);
    }

    /**
     * @Route("/ma-boutique/{id}/commandes/{order}/confirmer", name="seller_orders_confirm", requirements={"id": "[0-9\-]*", "order": "[0-9\-]*"})
     * @IsGranted("ROLE_MERCHANT")
     * @param Shop $shop
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     * @throws LoaderError
     * @throws Exception
     */
    public function confirm_order(Shop $shop, OrderRepository $orderRepository, Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $order = $orderRepository->find($request->attributes->get('order'));
        $order_infos = $request->request->all('order_confirm');
        $order->setDay(new DateTime($order_infos['day']))
            ->setStatus(1);
        $order_user = $order->getBuyer();
        $this->em->persist($order);
        $this->em->flush();
        $title = "Votre commande numéro " . $order->getOrderNumber() . " a été acceptée par le commerçant";
        $options = [];
        array_push($options, $order_user->getName(), $order->getOrderNumber(), $order_infos['day'], $order_infos['begin'], $order_infos['end']);
        if(isset($order_infos['message']) && $order_infos['message'] !== null) {
            array_push($options, $order_infos['message']);
        }
        (new MailerController)->send($this->mailer, $order_user->getEmail(), $title, $options, 'orderaccept');
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
        $title = "Votre commande numéro " . $order->getOrderNumber() . " est prête chez le commerçant";
        $options = [];
        array_push($options, $user->getName(), $order->getOrderNumber(), $order_infos['day'], $order_infos['begin'], $order_infos['end']);
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
        $title = "Votre commande numéro " . $order->getOrderNumber() . " a été annulée par le commerçant";
        $options = [];
        array_push($options, $user->getName(), $order->getOrderNumber());
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

    /**
     * @Route("/ma-boutique/creation", name="seller_create")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param PaymentRepository $paymentRepository
     * @param TagRepository $tagRepository
     * @return Response
     * @throws Exception
     */
    public function create(Request $request, PaymentRepository $paymentRepository, TagRepository $tagRepository): Response
    {
        $user = $this->getUser();
        $shop = new Shop();
        $form = $this->createForm(ShopForm::class, $shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = sprintf("%s-%s.%s", $shop->getName(), $shop->getPostalCode(), $image->guessExtension());

                try {
                    $image->move(
                        $this->getParameter('uploads/shops'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $shop->setImage($newFilename);
            }
            $default_payment = $paymentRepository->find(1);
            $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
            $shop
                ->setOwner($user)
                ->setCreatedAt(new DateTime('now', $dateTimeZoneFrance))
                ->setUpdatedAt(new DateTime('now', $dateTimeZoneFrance))
                ->setStatus(0)
                ->setIsBanned(0)
                ->setIsVerified(0)
                ->addPayment($default_payment)
                ->setSlug($shop->getSlug())
                ->setTag($tagRepository->find(1));
            dump($shop->getPayments());
            $this->em->persist($shop);
            $this->em->flush();
            $i = 0;
            $data = [];
            for($i; $i < 28; $i++) {
                $data[$i] = "00:00";
            }
            $d = 1;
            for ($k = 0; $k < 28; $k+=4) {
                $day = new OpeningHours();
                $day->setDay($d)
                    ->setShop($shop)
                    ->setStart(new DateTime($data[$k], new DateTimeZone("Europe/Paris")))
                    ->setEnd(new DateTime($data[$k+1], new DateTimeZone("Europe/Paris")));
                $day2 = new OpeningHours();
                $day2->setDay($d)
                    ->setShop($shop)
                    ->setStart(new DateTime($data[$k+2], new DateTimeZone("Europe/Paris")))
                    ->setEnd(new DateTime($data[$k+3], new DateTimeZone("Europe/Paris")));
                $this->em->persist($day);
                $this->em->persist($day2);
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

    /**
     * @Route("/ma-boutique/{id}/modifier", name="seller_edit", requirements={"id": "[0-9\-]*"})
     * @param Shop $shop
     * @param Request $request
     * @param PaymentRepository $paymentRepository
     * @param OpeningHoursRepository $openingHoursRepository
     * @return Response
     * @throws Exception
     */
    public function edit(Shop $shop, Request $request, PaymentRepository $paymentRepository, OpeningHoursRepository $openingHoursRepository): Response
    {
        $user = $this->getUser();
        $form_update = $this->createForm(ShopUpdateForm::class, $shop);
        $payments = $paymentRepository->findAll();
        $form_update->handleRequest($request);
        $form_delete = $this->createForm(ShopDeleteForm::class, $shop);
        $form_delete->handleRequest($request);

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

        if ($form_delete->isSubmitted() && $form_delete->isValid()) {
            $this->em->remove($shop);
            $this->em->flush();
            return $this->redirectToRoute('seller_choose');
        }

        $openingHours = $openingHoursRepository->findBy(["shop" => $shop]);
        dump($openingHours);
        if($request->getMethod() === "POST" && $request->request->get('day') !== null) {
            $data = $request->request->all();
            $day = $data['day'];
            unset($data['day']);
            $k = 0;
            foreach ($data as $key => $value) {
                $data[$k] = $value;
                unset($data[$key]);
                $k = $k + 1;
            }
            dump($data);
            $d = 1;
            foreach ($openingHours as $openingHour) {
                $this->em->remove($openingHour);
            }
            $this->em->flush();
            for ($i = 0; $i < count($data); $i+=4) {
                if($data[$i] !== "" && $data[$i+1] !== "") {
                    $day = new OpeningHours();
                    $day->setDay($d)
                        ->setShop($shop)
                        ->setStart(new DateTime($data[$i], new DateTimeZone("Europe/Paris")))
                        ->setEnd(new DateTime($data[$i+1], new DateTimeZone("Europe/Paris")));
                    $this->em->persist($day);
                    $this->em->flush();
                    $shop->addOpeningHour($day);
                }
                if($data[$i+2] !== "" && $data[$i+3] !== "") {
                    $day2 = new OpeningHours();
                    $day2->setDay($d)
                        ->setShop($shop)
                        ->setStart(new DateTime($data[$i+2], new DateTimeZone("Europe/Paris")))
                        ->setEnd(new DateTime($data[$i+3], new DateTimeZone("Europe/Paris")));
                    $this->em->persist($day2);
                    $this->em->flush();
                    $shop->addOpeningHour($day2);
                }
                $d = $d + 1;
            }
            $this->em->flush();
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


    /**
     * @Route("/ma-boutique/{id}/produits", name="seller_edit_products", requirements={"id": "[0-9\-]*"})
     * @param Shop $shop
     * @param ProductRepository $productRepository
     * @param Request $request
     * @param ProductTypeRepository $productTypeRepository
     * @return Response
     */
    public function products(Shop $shop, ProductRepository $productRepository, Request $request, ProductTypeRepository $productTypeRepository): Response
    {
        $user = $this->getUser();
        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product, ['id' => $shop->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $product->setName($product->getName())
                ->setDescription($product->getDescription())
                ->setShop($shop)
                ->setUnitPrice($product->getUnitPrice())
                ->setType($productTypeRepository->find(1));
            if($product->getUnitPriceDiscount() == null) {
                $product->setUnitPriceDiscount(null);
            } else {
                $product->setUnitPriceDiscount($product->getUnitPriceDiscount());
            }
            $limit = count($images);
            for ($i = 0; $i < $limit; $i++) {
                if($images[$i]) {
                    $newFilename = sprintf("%s-%s-%s-%s.%s", $product->getName(), $shop->getId(), $shop->getName(), $i, $images[$i]->guessExtension());
                    try {
                        $images[$i]->move(
                            $this->getParameter('uploads/products'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }

                    $product_images = $product->getImages();
                    array_push($product_images, $newFilename);
                    $product->setImages($product_images);
                }
            }
            $productType = $productTypeRepository->find(1);
            $productType->addProduct($product);
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
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setShop($shop);
            $this->em->persist($category);
            $this->em->flush();
        }

        $form_delete = $this->createForm(DeleteCategoryForm::class, $category);
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
     * @param ProductTypeRepository $productTypeRepository
     * @return Response
     */
    public function editProduct(ShopRepository $shopRepository, Request $request, ProductRepository $productRepository, ProductTypeRepository $productTypeRepository): Response
    {
        $user = $this->getUser();
        $product = $productRepository->find($request->attributes->get('product'));
        $shop = $shopRepository->find($request->attributes->get('id'));
        $form_update_product = $this->createForm(ProductForm::class, $product, ['id' => $shop->getId()]);
        $form_update_product->handleRequest($request);
        if($form_update_product->isSubmitted() && $form_update_product->isValid()) {
            $images = $form_update_product->get('images')->getData();
            $product->setName($product->getName())
                ->setDescription($product->getDescription())
                ->setUnitPrice($product->getUnitPrice())
                ->setType($productTypeRepository->find(1));
            if($product->getUnitPriceDiscount() == null) {
                $product->setUnitPriceDiscount(null);
            } else {
                $product->setUnitPriceDiscount($product->getUnitPriceDiscount());
            }
            $limit = count($images);
            for ($i = 0; $i < $limit; $i++) {
                if($images[$i]) {
                    $newFilename = sprintf("%s-%s-%s-%s.%s", $product->getName(), $shop->getId(), $shop->getName(), $i, $images[$i]->guessExtension());
                    try {
                        $images[$i]->move(
                            $this->getParameter('uploads/products'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }

                    $product_images = $product->getImages();
                    array_push($product_images, $newFilename);
                    $product->setImages($product_images);
                }
            }
            $productType = $productTypeRepository->find(1);
            $productType->addProduct($product);
            $this->em->persist($product);
            $this->em->flush();
            return $this->redirectToRoute('seller_edit_products', ['id' => $shop->getId()]);
        }

        return $this->render("seller/edit_product.html.twig", [
            'user' => $user,
            'shop' => $shop,
            'product' => $product,
            'form' => $form_update_product->createView()
        ]);
    }

    /**
     * @Route("/ma-boutique/{id}/produits/{product}/supprimer", name="seller_delete_product", requirements={"id": "[0-9\-]*", "product": "[0-9\-]*"})
     * @param ShopRepository $shopRepository
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete_product(ShopRepository $shopRepository, ProductRepository $productRepository, Request $request): RedirectResponse
    {
        $product = $productRepository->find($request->attributes->get('product'));
        $shop = $shopRepository->find($request->attributes->get('id'));
        $this->em->remove($product);
        $this->em->flush();
        return $this->redirectToRoute('seller_edit_products', ["id" => $shop->getId(), "product" => $product->getId()]);
    }
}
