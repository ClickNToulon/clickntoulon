<?php

namespace App\Domain\Buyer;

use App\Domain\Auth\User;
use App\Domain\Shop\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Basket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Basket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Basket[]    findAll()
 * @method Basket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class BasketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Basket::class);
    }

    public function findByUser(User|UserInterface $owner): array
    {
        return $this
            ->createQueryBuilder('b')
            ->where('b.owner = :user')
            ->setParameter('user', $owner)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByUserAndShop(User|UserInterface $user, Shop $shop): Basket|null
    {
        return $this
            ->createQueryBuilder('b')
            ->where('b.owner = :user AND b.shop = :shop')
            ->setParameters([
                'user' => $user,
                'shop' => $shop
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
