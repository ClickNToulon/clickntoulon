<?php

namespace App\Http\Controller\Seller;

use App\Domain\Shop\OpeningHours;
use App\Domain\Shop\OpeningHoursRepository;
use App\Domain\Shop\PaymentRepository;
use App\Domain\Shop\Shop;
use App\Domain\Shop\ShopRepository;
use App\Domain\Shop\TagRepository;
use App\Http\Form\Shop\ShopDeleteForm;
use App\Http\Form\Shop\ShopUpdateForm;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/ma-boutique", name: "seller_")]
class EditController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ShopRepository         $shopRepository,
        private readonly PaymentRepository      $paymentRepository,
        private readonly TagRepository          $tagRepository,
        private readonly OpeningHoursRepository $hoursRepository
    ){}

    #[Route(path: "/{id}/modifier", name: "edit", requirements: ["id" => "[0-9\-]*"])]
    #[IsGranted("ROLE_MERCHANT")]
    public function edit(Shop $shop, Request $request): Response
    {
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
        $openingHours = $this->hoursRepository->findBy(["shop" => $shop]);
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
            dump(count($data));
            dump($data);
            for ($i = 0; $i < count($data); $i+=4) {
                if ($data[$i] !== "" && $data[$i+1] !== "") {
                    [$day, $response] = $this->setOpeningHours($d, $shop, $data, $i);
                    if($response) {
                        return $response;
                    }
                    $this->em->persist($day);
                    $this->em->flush();
                    $shop->addOpeningHour($day);
                } else {
                    [$day, $response] = $this->setOpeningHours($d, $shop, null, $i);
                    if($response) {
                        return $response;
                    }
                    $this->em->persist($day);
                    $this->em->flush();
                    $shop->addOpeningHour($day);
                }
                if($data[$i+2] !== "" && $data[$i+3] !== "") {
                    [$day2, $response] = $this->setOpeningHours($d, $shop, $data, $i+2);
                    if($response) {
                        return $response;
                    }
                    $this->em->persist($day2);
                    $this->em->flush();
                    $shop->addOpeningHour($day2);
                } else {
                    [$day2, $response] = $this->setOpeningHours($d, $shop, null, $i+2);
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
            'form_update' => $form_update->createView(),
            'form_delete' => $form_delete->createView(),
            'payments' => $payments,
            'openingHours' => $openingHours
        ]);
    }

    private function setOpeningHours(int $d, Shop $shop, ?array $data, int $i): array
    {
        $day = new OpeningHours();
        try {
            $day->setDay($d)
                ->setShop($shop);
            if($data != null) {
                $day->setStart(new DateTime($data[$i], new DateTimeZone("Europe/Paris")))
                    ->setEnd(new DateTime($data[$i+1], new DateTimeZone("Europe/Paris")));
            } else {
                $day->setStart(null)
                    ->setEnd(null);
            }
        } catch (Exception) {
            $response = new Response('Test');
            return [null, $response];
        }

        return [$day, null];
    }
}