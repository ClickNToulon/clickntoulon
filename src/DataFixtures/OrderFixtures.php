<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $order = new Order();
        $order->setBasketId(1)
            ->setBuyerId(1)
            ->setDay(new \DateTime())
            ->setProductsId("1")
            ->setQuantity("2")
            ->setShopId(1)
            ->setStatus(1);
        $manager->persist($order);

        $manager->flush();
    }
}
