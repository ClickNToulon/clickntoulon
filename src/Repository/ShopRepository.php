<?php

namespace App\Repository;

use App\Entity\Shop;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    public function findAllVisible(): array
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.status >= 1')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function search($search_param): array
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.name LIKE :param')
            ->setParameter('param', '%'.$search_param.'%')
            ->andWhere('s.isBanned = false')
            ->getQuery()
            ->getResult();
    }

    public function findAllQuery(): Query
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.isBanned != true AND s.status >= 1')
            ->orderBy('s.id', 'DESC')
            ->getQuery();
    }

    public function home(): array
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.status > 1')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    public function choose(User $owner): QueryBuilder
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.owner = :owner')
            ->setParameter('owner', $owner);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findById($shopID): Shop|null
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.id = :shop_id')
            ->setParameter('shop_id', $shopID)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByUser($owner): array
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.owner = :user')
            ->setParameter('user', $owner)
            ->getQuery()
            ->getResult();
    }
}
