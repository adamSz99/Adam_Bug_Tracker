<?php
/**
 * Report controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\ReportType;
use App\Entity\Report;
use App\Entity\User;
use App\Repository\ReportRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ReportControllerTest.
 */
class ReportControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/reports';

    /**
     * Test report index route.
     */
    public function testReportIndexGetRoute(): void
    {
        $this->httpClient->request('GET', '/reports');
        $status = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(200, $status);
    }

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test report show route.
     */
    public function testReportShowRoute(): void
    {
        // given
        $testUser = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testuser@example.com');
        $category = $this->createCategory($testUser);
        $report = $this->createReport($testUser, $category);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$report->getId());
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }

    /**
     * Test report create route.
     */
    public function testReportCreateRoute(): void
    {
        // given
        $testUser = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testuser@example.com');
        $this->createCategory($testUser);

        $this->httpClient->loginUser($testUser);

        $reportTitle = '1111TITLE';
        $reportDescription = '1111TITLE';
        $reportType = ReportType::FEATURE_REQUEST->value;

        $categoryRepository = static::getContainer()
            ->get(CategoryRepository::class);

        // when
        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $form = $crawler->selectButton('Zapisz')->form();

        $form['report[title]'] = $reportTitle;
        $form['report[description]'] = $reportDescription;
        $form['report[type]'] = $reportType;
        $form['report[category]'] = (string) $categoryRepository->findOneByName('CATEGORY')->getId();
        $form['report[resolved]'] = '1';

        $this->httpClient->submit($form);
        $reportRepository = static::getContainer()->get(ReportRepository::class);

        // then
        $report = $reportRepository->find(2);

        $this->assertEquals($reportTitle, $report->getTitle());
        $this->assertEquals($reportDescription, $report->getDescription());
        $this->assertEquals($reportType, $report->getType());

        $result = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(302, $result);
    }

    /**
     * Test report edit route.
     */
    public function testReportEditRoute(): void
    {
        // given
        $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'test@example.com');
        $category = $this->createCategory($user);
        $report = $this->createReport($user, $category);

        $this->httpClient->loginUser($user);

        $reportRepository = static::getContainer()
            ->get(ReportRepository::class);

        $categoryRepository = static::getContainer()
            ->get(CategoryRepository::class);

        $reportRepository->save($report);

        $reportTitle = '2222TITLE';
        $reportDescription = '2222TITLE';
        $reportType = ReportType::FEATURE_REQUEST->value;

        $newlyCreatedReport = $reportRepository->findOneByTitle('TITLE');

        // when
        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$newlyCreatedReport->getId().'/edit');
        $form = $crawler->selectButton('Edytuj')->form();

        $form['report[title]'] = $reportTitle;
        $form['report[description]'] = $reportDescription;
        $form['report[type]'] = $reportType;
        $form['report[category]'] = (string) $categoryRepository->findOneByName('CATEGORY')->getId();
        $form['report[resolved]'] = '1';

        $this->httpClient->submit($form);

        // then
        $result = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $result);
    }

    /**
     * Create user
     *
     * @param array  $roles
     * @param string $email
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
     * Crate report
     *
     * @param User        $user
     * @param Category    $category
     * @param string      $title
     * @param string      $description
     * @param string|null $type
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
     * Create test category.
     *
     * @param User   $user
     * @param string $name
     *
     * @return Category
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
