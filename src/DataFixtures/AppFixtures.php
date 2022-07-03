<?php

namespace App\DataFixtures;

use App\Domain\Shop\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tag = new Tag();
        $tag->setName("Boulangerie");
        $manager->persist($tag);

        $this->addReference('tag', $tag);
        $manager->flush();
    }
}
