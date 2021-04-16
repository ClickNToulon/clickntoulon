<?php

namespace App\Repository;

use App\Entity\Shop;
use App\Entity\TimeTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimeTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeTable[]    findAll()
 * @method TimeTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeTable::class);
    }

    // /**
    //  * @return TimeTable[] Returns an array of TimeTable objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimeTable
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findById(Shop $shop)
    {
        return $this->createQueryBuilder('t')
            ->where('t.shop_id = :shop_id')
            ->setParameter('shop_id', $shop->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
