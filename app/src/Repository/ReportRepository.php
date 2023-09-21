<?php

/**
 * Report repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Report>
 *
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    /**
     * Save.
     *
     * @param Report $report Report
     */
    public function save(Report $report): void
    {
        $this->_em->persist($report);
        $this->_em->flush();
    }

    /**
     * Delete.
     *
     * @param Report $report Report
     */
    public function delete(Report $report): void
    {
        $this->_em->remove($report);
        $this->_em->flush();
    }

    /**
     * Get all reports.
     *
     * @param array $filters Filters
     *
     * @return QueryBuilder QueryBuilder
     */
    public function queryAll(array $filters): QueryBuilder
    {
        $result = $this
            ->getOrCreateQueryBuilder()
            ->select('partial report.{id, createdAt, updatedAt, title, description, type, resolved}, partial category.{id, name}')
            ->join('report.category', 'category')
            ->orderBy('report.createdAt', 'DESC');

        return $this->filter($result, $filters);
    }

    /**
     * Count by category.
     *
     * @param Category $category Category
     *
     * @return mixed Mixed
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): mixed
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('report.id'))
            ->where('report.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get or create new query builder.
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('report');
    }

    /**
     * Filter.
     *
     * @param QueryBuilder $queryBuilder Query builder
     * @param array        $filters      Filters
     *
     * @return QueryBuilder Array
     */
    private function filter(QueryBuilder $queryBuilder, array $filters = []): QueryBuilder
    {
        if (isset($filters['category']) && $filters['category'] instanceof Category) {
            $queryBuilder->andWhere('category = :category')->setParameter('category', $filters['category']);
        }

        return $queryBuilder;
    }
}
