<?php

namespace App\Domain\Product;

use App\Domain\Shop\Shop;
use App\Helper\FilterData\SearchProductData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class ProductRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
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

    public function findSearch(SearchProductData $data): PaginationInterface
    {
        $query = $this->getSearchQuery($data)->getQuery();
        return $this->paginator->paginate(
            $query,
            $data->page,
            6
        );
    }

    /**
     * Récupère le prix minimum et maximum correspondant à une recherche
     * @param SearchProductData $search
     * @return float
     */
    public function findMinMax(SearchProductData $search): float
    {
        $results = $this->getSearchQuery($search, true)
            ->select('MAX(p.unitPrice) as max')
            ->getQuery()
            ->getResult();
        return (float)$results[0]['max'];
    }

    private function getSearchQuery(SearchProductData $search, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('pt', 'p', 's')
            ->join('p.type', 'pt')
            ->join('p.shop', 's');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :search')
                ->setParameter('search', "%$search->q%");
        }

        if (!empty($search->min)) {
            $query = $query
                ->andWhere('p.unitPrice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max)) {
            $query = $query
                ->andWhere('p.unitPrice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->types)) {
            $query = $query
                ->andWhere('pt.id IN (:types)')
                ->setParameter('types', $search->types);
        }

        if (!empty($search->shop)) {
            $query = $query
                ->andWhere('s.id IN (:shop)')
                ->setParameter('shop', $search->shop);
        }

        return $query;
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
