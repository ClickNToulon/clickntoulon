<?php

namespace App\DataFixtures;

use App\Entity\Payment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $payment = new Payment();
        $payment->setName("Espèces");

        $manager->persist($payment);
        $manager->flush();
    }
}
