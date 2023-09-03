<?php
/**
 * Report fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Enum\ReportType;
use App\Entity\Report;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class ReportFixtures.
 */
class ReportFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(40, 'reports', function (int $i) {
            $report = new Report();
            $report->setTitle($this->faker->sentence);
            $report->setDescription($this->faker->realText);
            $report->setType($this->faker->randomElement([
                ReportType::BUG->value,
                ReportType::UNKNOWN->value,
                ReportType::IMPROVEMENT->value,
                ReportType::FEATURE_REQUEST->value,
            ]));
            $report->setResolved($this->faker->boolean);
            $report->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $report->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            /** @var Category $category */
            $category = $this->getRandomReference('categories');
            $user = $this->getRandomReference('users');
            $report->setCategory($category);
            $report->setAuthor($user);

            return $report;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, UserFixtures::class];
    }
}
