<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TimeAwareTrait
{
    #[ORM\Column]
    protected ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    protected ?\DateTimeImmutable $updated_at = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->created_at = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updated_at = $updatedAt;
    }
}
