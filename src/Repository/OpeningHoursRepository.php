<?php

namespace App\Repository;

use App\Entity\OpeningHours;
use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OpeningHours|null find($id, $lockMode = null, $lockVersion = null)
 * @method OpeningHours|null findOneBy(array $criteria, array $orderBy = null)
 * @method OpeningHours[]    findAll()
 * @method OpeningHours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class OpeningHoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningHours::class);
    }

    public function findByShop(Shop $shop): array
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery()
            ->getResult();
    }
}