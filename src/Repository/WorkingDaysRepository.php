<?php

namespace App\Repository;

use App\Entity\WorkingDays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkingDays|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkingDays|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkingDays[]    findAll()
 * @method WorkingDays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkingDaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkingDays::class);
    }

    // /**
    //  * @return WorkingDays[] Returns an array of WorkingDays objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorkingDays
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
