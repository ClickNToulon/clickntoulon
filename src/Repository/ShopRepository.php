<?php

namespace App\Repository;

use App\Entity\Shop;
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
     * @return int|mixed|string
     */
    public function findAllVisible()
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
            ->andWhere('s.banned = false')
            ->getQuery()
            ->getResult();
    }

    public function findAllQuery(): Query
    {
        return $this->createQueryBuilder('s')
            ->where('s.banned != true')
            ->getQuery();
    }

    public function home()
    {
        return $this->createQueryBuilder('s')
            ->where('s.status >= 1')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return QueryBuilder
     */
    public function choose($id): QueryBuilder
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.owner_id = :id')
            ->setParameter('id', $id);
    }

    // /**
    //  * @return Shop[] Returns an array of Shop objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Shop
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
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

    public function findAllByUser($id)
    {
        return $this->createQueryBuilder('s')
            ->where('s.owner_id = :user_id')
            ->setParameter('user_id', array($id))
            ->getQuery()
            ->getResult();
    }
}