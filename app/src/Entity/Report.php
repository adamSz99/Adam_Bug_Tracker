<?php
/**
 * Report
 */

namespace App\Entity;

use App\Repository\ReportRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Report
 */
#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    /**
     * Primary key
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Report title
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5, max: 255)]
    private ?string $title = null;

    /**
     * Description
     *
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $description = null;

    /**
     * If report is resolved or not
     *
     * @var bool|null
     */
    #[ORM\Column]
    private ?bool $resolved = null;

    /**
     * Report type
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /**
     * Report category
     *
     * @var Category|null
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * Created at.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Assert\Type(DateTimeImmutable::class)]
    private ?DateTimeImmutable $createdAt;

    /**
     * Updated at.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'update')]
    #[Assert\Type(DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * Get created at.
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set created at.
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get updated at.
     *
     * @return null|DateTimeImmutable Updated at
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Set updated at.
     *
     * @param null|DateTimeImmutable Updated at
     */
    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get id
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get title
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title Titlte
     *
     * @return $this Report
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get description
     *
     * @return string|null Description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Description
     *
     * @param string|null $description Description
     *
     * @return $this Report
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Is resolved
     *
     * @return bool|null Is resolved
     */
    public function isResolved(): ?bool
    {
        return $this->resolved;
    }

    /**
     * Set resolved
     *
     * @param bool $resolved Resolved
     *
     * @return $this Report
     */
    public function setResolved(bool $resolved): static
    {
        $this->resolved = $resolved;

        return $this;
    }

    /**
     * Get type
     *
     * @return string|null Type
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type Type
     *
     * @return $this Report
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category|null Category or null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param Category|null $category Category or null
     *
     * @return $this Report
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get author
     *
     * @return User|null User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Set author
     *
     * @param User|null $author User or null
     *
     * @return $this Report
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
}
