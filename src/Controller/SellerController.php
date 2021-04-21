<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Shop;
use App\Form\CategoryType;
use App\Form\ChooseShop;
use App\Form\ProductType;
use App\Form\ShopDeleteForm;
use App\Form\ShopType;
use App\Form\ShopUpdateForm;
use App\Repository\CategoryRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
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

    public function __construct(EntityManagerInterface $em, ShopRepository $shopRepository)
    {
        $this->em = $em;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @Route("/ma-boutique/{id}", name="seller_index", requirements={"id": "[0-9\-]*"})
     * @IsGranted("ROLE_MERCHANT")
     * @param Shop $shop
     * @return Response
     */
    public function index(Shop $shop): Response
    {
        $user = $this->getUser();
        $shopsUser = $this->shopRepository->findAllByUser($user->getId());
        if (!in_array($shop, $shopsUser)) {
            return new Response($this->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
        }
        return $this->render('seller/index.html.twig', [
            'user' => $user,
            'shop' => $shop
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
            dump($data);
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
    public function edit(Shop $shop, Request $request, PaymentRepository $paymentRepository): Response
    {
        $user = $this->getUser();
        $form_update = $this->createForm(ShopUpdateForm::class, $shop);
        $payments = $paymentRepository->findAll();
        $form_update->handleRequest($request);

        $form_delete = $this->createForm(ShopDeleteForm::class, $shop);
        $form_delete->handleRequest($request);

        if ($form_update->isSubmitted() && $form_update->isValid()) {
            $payments_add = $request->request->all('payment');
            foreach ($payments_add as $key => $value) {
                $payment_add[] = $paymentRepository->find($value);
                foreach ($payment_add as $key2 => $value2) {
                    $shop->addPayment($value2);
                }
            }

            $this->em->persist($shop);
            $this->em->flush();
            $this->addFlash('success', 'Votre boutique a bien été modifiée');
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

        $categories = $categoryRepository->findAllByShop($shop);
        dump($categories);
        $total_categories = count($categories);
        return $this->render("seller/categories.html.twig", [
            'user' => $user,
            'shop' => $shop,
            'categories' => $categories,
            'total_categories' => $total_categories,
            'form' => $form->createView()
        ]);
    }

}