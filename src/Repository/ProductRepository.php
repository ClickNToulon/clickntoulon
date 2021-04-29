<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function search($search_param)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :param')
            ->setParameter('param', '%'.$search_param.'%')
            ->getQuery()
            ->getResult();
    }

    public function home()
    {
        return $this->createQueryBuilder('p')
            ->where('p.status >= 0')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllQuery(): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.status >= 0')
            ->getQuery();
    }

    public function findAllByShopQuery(Shop $shop): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.shop_id = :shop_id')
            ->setParameter('shop_id', $shop->getId())
            ->getQuery();
    }

    public function findAllByShop(Shop $shop)
    {
        return $this->createQueryBuilder('p')
            ->where('p.shop_id = :shop_id')
            ->setParameter('shop_id', $shop->getId())
            ->getQuery()
            ->getResult();
    }
}