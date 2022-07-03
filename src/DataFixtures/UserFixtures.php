<?php

namespace App\DataFixtures;

use App\Domain\Auth\User;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setSurname('Admin')
             ->setName('Admin')
             ->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')))
             ->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')))
             ->setEmail('admin@test.fr')
             ->setRoles(['ROLE_USER', 'ROLE_MERCHANT', 'ROLE_ADMIN'])
             ->setIsBanned(false)
             ->setIsVerified(true)
             ->setPassword($this->passwordHasher->hashPassword($user, 'admin'));
        $this->addReference('user', $user);
        $manager->persist($user);
        $manager->flush();
    }
}