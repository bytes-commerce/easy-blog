<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Entity;

use BytesCommerce\EasyBlog\Enum\BlogStateEnum;
use BytesCommerce\EasyBlog\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post implements TimeAwareInterface
{
    use TimeAwareTrait;
    use SeoDataTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'posts')]
    private Collection $categories;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $excerpt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $credits = null;

    #[ORM\Column(nullable: true)]
    private ?int $access_counter = null;

    /**
     * @var Collection<int, Faq>
     */
    #[ORM\OneToMany(targetEntity: Faq::class, mappedBy: 'post', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $faqs;

    #[ORM\Column]
    private BlogStateEnum $status;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->faqs = new ArrayCollection();
        $this->status = BlogStateEnum::DRAFT;
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): static
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getCredits(): ?string
    {
        return $this->credits;
    }

    public function setCredits(?string $credits): static
    {
        $this->credits = $credits;

        return $this;
    }

    public function getAccessCounter(): ?int
    {
        return $this->access_counter;
    }

    public function setAccessCounter(?int $access_counter): static
    {
        $this->access_counter = $access_counter;

        return $this;
    }

    /**
     * @return Collection<int, Faq>
     */
    public function getFaqs(): Collection
    {
        return $this->faqs;
    }

    public function addFaq(Faq $faq): static
    {
        if (!$this->faqs->contains($faq)) {
            $this->faqs->add($faq);
            $faq->setPost($this);
        }

        return $this;
    }

    public function removeFaq(Faq $faq): static
    {
        if ($this->faqs->removeElement($faq)) {
            if ($faq->getPost() === $this) {
                $faq->setPost(null);
            }
        }

        return $this;
    }

    public function getStatus(): BlogStateEnum
    {
        return $this->status;
    }

    public function setStatus(BlogStateEnum $status): void
    {
        $this->status = $status;
    }
}
