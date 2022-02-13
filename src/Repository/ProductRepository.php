<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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

    public function search($search_param): array
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.name LIKE :param')
            ->setParameter('param', '%'.$search_param.'%')
            ->getQuery()
            ->getResult();
    }

    public function home(): array
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.id >= 0')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();
    }

    public function findAllQuery(): Query
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.id >= 0')
            ->getQuery();
    }

    public function findAllByShopQuery(Shop $shop): Query
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery();
    }

    public function findAllByShop(Shop $shop): array
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery()
            ->getResult();
    }
}
