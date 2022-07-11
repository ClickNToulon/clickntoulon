<?php

namespace App\Domain\Buyer;

use App\Domain\Auth\User;
use App\Domain\Shop\Shop;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
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

    public function findAllPriorTodayForShop(Shop $shop, DateTime $dateTime)
    {
        return $this->createQueryBuilder('o')
            ->where('o.shop = :shop')
            ->setParameter('shop', $shop)
            ->andWhere('o.day < :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findTotalForShop(Shop $shop)
    {
        return $this->createQueryBuilder('o')
            ->select('SUM(o.total) as sum')
            ->where('o.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
