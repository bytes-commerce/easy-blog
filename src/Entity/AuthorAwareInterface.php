<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Entity;

use Doctrine\Common\Collections\Collection;

interface AuthorAwareInterface
{
    public function addPost(Post $post): self;

    public function removePost(Post $post): self;

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection;
}
