<?php

namespace App\DataFixtures;

use App\Domain\Shop\Shop;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ShopFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $shop = new Shop();
        $shop->setStatus(2)
            ->setName("Hello Test")
            ->setAddress("Test address")
            ->setBanned(false)
            ->setCity("Toulon")
            ->setPostalCode(83000)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setDescription("test description")
            ->setIsVerified(true)
            ->setOwnerId(1)
            ->setSlug("test")
            ->setPhone(6789789)
            ->setEmail("test@test.fr")
            ->setCover("")
            ->setTag(0);
        $manager->persist($shop);

        $manager->flush();
    }
}