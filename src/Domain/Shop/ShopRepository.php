<?php

namespace App\Domain\Shop;

use App\Domain\Auth\User;
use App\Helper\FilterData\SearchShopData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class ShopRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Shop::class);
        $this->paginator = $paginator;
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
            ->setMaxResults(2)
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

    public function findSearch(SearchShopData $data): array
    {
        $query = $this->getSearchQuery($data)->getQuery();
        return [
            count($this->findAll()),
            $this->paginator->paginate(
                $query,
                $data->page,
                6
            )
        ];
    }

    private function getSearchQuery(SearchShopData $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('t', 's')
            ->join('s.tag', 't');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('s.name LIKE :search')
                ->setParameter('search', "%$search->q%");
        }

        if (!empty($search->city)) {
            $query = $query
                ->andWhere('s.city LIKE :city')
                ->setParameter('city', "%$search->city%");
        }

        if (!empty($search->postalCode)) {
            $query = $query
                ->andWhere('s.postalCode LIKE :postalCode')
                ->setParameter('postalCode', "%$search->postalCode%");
        }

        if (!empty($search->tag)) {
            $query = $query
                ->andWhere('t.id IN (:tag)')
                ->setParameter('tag', $search->tag);
        }

        return $query;
    }
}
