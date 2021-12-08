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

    /**
     * @return mixed
     */
    public function findAllVisible(): mixed
    {
        return $this->createQueryBuilder('s')
            ->where('s.status >= 1')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function search($search_param)
    {
        return $this->createQueryBuilder('s')
            ->where('s.name LIKE :param')
            ->setParameter('param', '%'.$search_param.'%')
            ->andWhere('s.isBanned != false')
            ->getQuery()
            ->getResult();
    }

    public function findAllQuery(): Query
    {
        return $this->createQueryBuilder('s')
            ->where('s.isBanned != true')
            ->getQuery();
    }

    public function home()
    {
        return $this->createQueryBuilder('s')
            ->where('s.status > 1')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $owner
     * @return QueryBuilder
     */
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
    public function findById($shopID)
    {
        return $this->createQueryBuilder('s')
            ->where('s.id = :shop_id')
            ->setParameter('shop_id', $shopID)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByUser($owner)
    {
        return $this->createQueryBuilder('s')
            ->where('s.owner = :user')
            ->setParameter('user', $owner)
            ->getQuery()
            ->getResult();
    }
}
