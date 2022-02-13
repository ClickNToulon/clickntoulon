<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllByShop(Shop $shop): array
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.shop = :shop_id')
            ->setParameter('shop_id', $shop)
            ->getQuery()
            ->getResult();
    }

    public function findAllByShopQuery($id): QueryBuilder
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.shop = :shop_id')
            ->setParameter('shop_id', $id);
    }
}
