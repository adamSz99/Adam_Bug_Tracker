<?php

/**
 * Report service.
 */

namespace App\Service;

use App\Entity\Report;
use App\Repository\ReportRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ReportService.
 */
class ReportService
{
    /**
     * Report repository.
     */
    private ReportRepository $reportRepository;

    /**
     * Paginator interface.
     */
    private PaginatorInterface $paginator;

    /**
     * Category service.
     */
    private CategoryService $categoryService;

    /**
     * Constructor.
     *
     * @param ReportRepository   $reportRepository Report repository
     * @param PaginatorInterface $paginator        Paginator
     * @param CategoryService    $categoryService  Category service
     */
    public function __construct(ReportRepository $reportRepository, PaginatorInterface $paginator, CategoryService $categoryService)
    {
        $this->reportRepository = $reportRepository;
        $this->paginator = $paginator;
        $this->categoryService = $categoryService;
    }

    /**
     * Save.
     *
     * @param Report $report Report
     */
    public function save(Report $report): void
    {
        $this->reportRepository->save($report);
    }

    /**
     * Delete.
     *
     * @param Report $report Report
     */
    public function delete(Report $report): void
    {
        $this->reportRepository->delete($report);
    }

    /**
     * Get paginated list.
     *
     * @param int   $page    Page number
     * @param array $filters Filters
     *
     * @return PaginationInterface Array
     */
    public function getPaginatedList(int $page, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->reportRepository->queryAll($filters),
            $page,
            ReportRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Prepare filters.
     *
     * @param array $filters Filters
     *
     * @return array Array
     */
    private function prepareFilters(array $filters): array
    {
        $resultFilters = [];

        if (!empty($filters['category_id'])) {
            $category = $this->categoryService->findOneById($filters['category_id']);
            if (null !== $category) {
                $resultFilters['category'] = $category;
            }
        }

        return $resultFilters;
    }
}
