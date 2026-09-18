<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Repository;

use BytesCommerce\EasyBlog\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractServiceRepository<Category>
 */
final class CategoryRepository extends AbstractServiceRepository implements CategoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
}
