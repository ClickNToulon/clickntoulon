<?php

namespace App\Repository;

use App\Entity\Basket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findByUser($id)
    {
        return $this->createQueryBuilder('b')
            ->where('b.owner_id = :user_id')
            ->setParameter('user_id', $id)
            ->getQuery()
            ->getResult();
    }

    public function  findOneByShop($id) {
        return $this->createQueryBuilder('b')
            ->where('b.shop_id = :shop_id')
            ->setParameter('shop_id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}
