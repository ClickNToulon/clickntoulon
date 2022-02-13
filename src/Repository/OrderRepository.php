<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Shop;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function findAllByUser(User $buyer): array
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.buyer = :user')
            ->setParameter('user', $buyer)
            ->getQuery()
            ->getResult();
    }

    public function getAllShop(Shop $shop): Query
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.shop = :shop and o.status < 3')
            ->setParameter('shop', $shop)
            ->getQuery();
    }

    public function findLast4ByUser(User|UserInterface $buyer): array
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.buyer = :user AND o.status < 6')
            ->setParameter('user', $buyer)
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    public function findByNumber(string $number): array
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.number = :number AND o.status < 6')
            ->setParameter('number', $number)
            ->getQuery()
            ->getResult();
    }
}
