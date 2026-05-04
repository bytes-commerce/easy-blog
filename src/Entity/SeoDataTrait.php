<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SeoDataTrait
{
    #[ORM\Column]
    protected ?string $seoTitle = null;

    #[ORM\Column]
    protected ?string $seoDescription = null;

    #[ORM\Column]
    protected ?string $seoKeywords = null;

    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(?string $seoTitle): void
    {
        $this->seoTitle = $seoTitle;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seoDescription;
    }

    public function setSeoDescription(?string $seoDescription): void
    {
        $this->seoDescription = $seoDescription;
    }

    public function getSeoKeywords(): ?string
    {
        return $this->seoKeywords;
    }

    public function setSeoKeywords(?string $seoKeywords): void
    {
        $this->seoKeywords = $seoKeywords;
    }
}
