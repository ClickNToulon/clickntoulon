<?php

namespace App\Repository;

use App\Entity\Basket;
use App\Entity\Shop;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Basket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Basket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Basket[]    findAll()
 * @method Basket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Basket::class);
    }

    public function findByUser(User $owner)
    {
        return $this->createQueryBuilder('b')
            ->where('b.owner = :user')
            ->setParameter('user', $owner)
            ->getQuery()
            ->getResult();
    }

    public function findByUserAndShop(User|UserInterface $user, Shop $shop) {
        return $this->createQueryBuilder('b')
            ->where('b.owner = :user AND b.shop = :shop')
            ->setParameters([
                'user' => $user,
                'shop' => $shop
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}
