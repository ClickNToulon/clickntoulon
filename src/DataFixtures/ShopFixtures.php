<?php

namespace App\DataFixtures;

use App\Domain\Auth\UserRepository;
use App\Domain\Shop\Shop;
use App\Domain\Shop\TagRepository;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ShopFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $shop = new Shop();
        $shop->setStatus(2)
            ->setName("Hello Test")
            ->setAddress("Test address")
            ->setIsBanned(false)
            ->setCity("Toulon")
            ->setPostalCode(83000)
            ->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')))
            ->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')))
            ->setDescription("test description")
            ->setIsVerified(true)
            ->setImage("https://www.google.fr/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png")
            ->setOwner($this->getReference('user'))
            ->setSlug("test")
            ->setPhone(6789789)
            ->setEmail("test@test.fr")
            ->setTag($this->getReference('tag'));
        $manager->persist($shop);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            AppFixtures::class
        ];
    }
}