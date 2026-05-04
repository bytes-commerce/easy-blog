<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Repository;

use BytesCommerce\EasyBlog\Entity\Category;
use BytesCommerce\EasyBlog\Entity\Post;
use BytesCommerce\EasyBlog\Enum\BlogStateEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

final class PostRepository extends AbstractServiceRepository implements PostRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPostsByCategory(Category $category): Collection
    {
        $query = $this->createQueryBuilder('p');

        $query->leftJoin('p.categories', 'c')
            ->where('c.id = :category')
            ->setParameter('category', $category->getId());

        $query->andWhere('p.status = :status')
            ->setParameter('status', BlogStateEnum::PUBLISHED);

        $query->orderBy('p.updated_at', 'DESC');

        $result = $query->getQuery()->getResult();

        return new ArrayCollection($result);
    }
}
