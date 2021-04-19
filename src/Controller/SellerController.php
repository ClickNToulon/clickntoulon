<?php


namespace App\Controller;


use App\Entity\Shop;
use App\Form\ChooseShop;
use App\Form\ShopType;
use App\Repository\ShopRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SellerController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    /**
     * @var \App\Repository\ShopRepository
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
        $form = $this->createForm(ChooseShop::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('select')->getViewData();
            dump($data);
            $shop = $this->shopRepository->find($data);
            return $this->redirectToRoute('seller_index', ['id' => $shop->getId()]);
        }
        return $this->render('seller/choose.html.twig', [
            'shops' => $shops,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ma-boutique/creation", name="seller_create")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        $shop = new Shop();
        $form = $this->createForm(ShopType::class, $shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
            $shop
                ->setEmail($shop->getEmail())
                ->setName($shop->getName())
                ->setPhone($shop->getPhone())
                ->setAddress($shop->getAddress())
                ->setPostalCode($shop->getPostalCode())
                ->setCity($shop->getCity())
                ->setDescription($shop->getDescription())
                ->setOwnerId($user->getId())
                ->setCreatedAt(new DateTime('now', $dateTimeZoneFrance))
                ->setUpdatedAt(new DateTime('now', $dateTimeZoneFrance))
                ->setStatus(0)
                ->setBanned(0)
                ->setIsVerified(0)
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

}