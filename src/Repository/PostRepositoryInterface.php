<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Repository;

use BytesCommerce\EasyBlog\Entity\Category;
use BytesCommerce\EasyBlog\Entity\Post;
use Doctrine\Common\Collections\Collection;

/**
 * Interface PostRepositoryInterface.
 *
 * @method Post[]    findAll()
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends AbstractServiceRepositoryInterface<Post>
 */
interface PostRepositoryInterface extends AbstractServiceRepositoryInterface
{
    /**
     * @return Collection<int, Post>
     */
    public function findPostsByCategory(Category $category): Collection;
}
