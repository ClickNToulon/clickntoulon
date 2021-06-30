<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findAllByUser($id)
    {
        return $this->createQueryBuilder('o')
            ->where('o.buyer_id = :user_id')
            ->setParameter('user_id', $id)
            ->getQuery()
            ->getResult();
    }

    public function getAllShop($id) {
        return $this->createQueryBuilder('o')
            ->where('o.shop_id = :shop_id and o.status < 4')
            ->setParameter('shop_id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findLast4ByUser($id)
    {
        return $this->createQueryBuilder('o')
            ->where('o.buyer_id = :user_id')
            ->setParameter('user_id', $id)
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }
}
