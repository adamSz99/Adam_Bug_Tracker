<?php

/**
 * Categories controller tests.
 */

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Entity\Enum\ReportType;
use App\Entity\Category;
use App\Entity\Report;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Repository\ReportRepository;

/**
 * Class CategoryControllerTest
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * Test route
     *
     * @const string
     */
    public const TEST_ROUTE = '/category';

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test category index action
     */
    public function testCategoryIndexAction(): void
    {
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(200, $resultHttpStatusCode);
    }

    /**
     * Test category show action
     */
    public function testCategoryShowAction(): void
    {
        // given
        $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testuser@example.com');
        $category = new Category();
        $category->setName('TEST_CATEGORY');
        $category->setAuthor($user);
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);

        // when
        $categoryRepository->save($category);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$category->getId());
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }

    /**
     * Create user
     *
     * @param array  $roles User roles
     * @param string $email Email
     *
     * @return User
     */
    protected function createUser(array $roles, string $email): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword(
            static::getContainer()
                ->get('security.password_hasher')
                ->hashPassword(
                    $user,
                    '@*&GC(@*#)D(%@#)Y12)234(!@'
                )
        );

        static::getContainer()
            ->get(UserRepository::class)
            ->save($user);

        return $user;
    }

    /**
     * Create report
     *
     * @param User        $user         User
     * @param Category    $category     Category
     * @param string      $title        Title
     * @param string      $description  Description
     * @param string|null $type         Type
     *
     * @return Report
     */
    private function createReport(User $user, Category $category, string $title = 'TITLE', string $description = 'DESCRIPTION', ?string $type = null): Report
    {
        $report = new Report();
        $report->setTitle($title);
        $report->setAuthor($user);
        $report->setType($type ?? ReportType::UNKNOWN->value);
        $report->setDescription($description);
        $report->setCategory($category);
        $report->setResolved(false);
        $report->setCreatedAt(new \DateTimeImmutable('now'));
        $report->setUpdatedAt(new \DateTimeImmutable('now'));

        self::getContainer()
            ->get(ReportRepository::class)
            ->save($report);

        return $report;
    }

    /**
     * Create test category
     *
     * @param User   $user User
     * @param string $name Name
     *
     * @return Category Category
     */
    private function createCategory(User $user, string $name = 'CATEGORY'): Category
    {
        $category = new Category();
        $category->setName($name);
        $category->setAuthor($user);

        self::getContainer()
            ->get(CategoryRepository::class)
            ->save($category);

        return $category;
    }
}

