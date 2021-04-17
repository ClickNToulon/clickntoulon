<?php


namespace App\Controller;


use App\Entity\Shop;
use App\Form\ShopType;
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
    private $em;

    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }

    /**
     * @Route("/ma-boutique", name="seller_index")
     * @IsGranted("ROLE_MERCHANT")
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('seller/index.html.twig', [
            'user' => $user
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