<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Shop\OpeningHours;
use App\Domain\Shop\Payment;
use App\Domain\Shop\PaymentRepository;
use App\Domain\Shop\Shop;
use App\Domain\Shop\ShopRepository;
use App\Domain\Shop\Tag;
use App\Http\Form\Shop\ChooseShopForm;
use App\Http\Form\Shop\ShopForm;
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
        private readonly EntityManagerInterface $em,
        private readonly ShopRepository         $shopRepository,
        private readonly PaymentRepository      $paymentRepository
    ){}

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
            'form' => $form->createView()
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
                } catch (FileException) {}
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
            'form' => $form->createView()
        ]);
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
                ->setTag($tag);
        } catch (Exception) {
            $response = new Response($this->render('bundles/TwigBundle/Exception/error500.html.twig'), Response::HTTP_INTERNAL_SERVER_ERROR);
            return [null, $response];
        }
        return [$shop, null];
    }
}