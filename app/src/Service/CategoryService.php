<?php

/**
 * Category service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ReportRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService
{
    /**
     * Category repository.
     */
    private CategoryRepository $categoryRepository;

    /**
     * Report repository.
     */
    private ReportRepository $reportRepository;

    /**
     * Paginator interface.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param CategoryRepository $categoryRepository Category repository
     * @param ReportRepository   $reportRepository   Report repository
     * @param PaginatorInterface $paginator          Paginator
     */
    public function __construct(CategoryRepository $categoryRepository, ReportRepository $reportRepository, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        $this->categoryRepository = $categoryRepository;
        $this->reportRepository = $reportRepository;
    }

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    /**
     * Delete entity.
     *
     * @param Category $category Category entity
     *
     * @return bool Result
     */
    public function delete(Category $category): bool
    {
        if (!$this->canBeDeleted($category)) {
            return false;
        }

        $this->categoryRepository->delete($category);

        return true;
    }

    /**
     * Can be deleted.
     *
     * @param Category $category Category entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            return !($this->reportRepository->countByCategory($category) > 0);
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page
     *
     * @return PaginationInterface Pagination
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page,
            ReportRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Find one.
     *
     * @param int $id Id
     *
     * @return Category|null Category or null
     */
    public function findOneById(int $id): ?Category
    {
        return $this->categoryRepository->findOneById($id);
    }
}
