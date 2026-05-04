<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Repository;

use BytesCommerce\EasyBlog\Entity\Category;

/**
 * Interface CategoryRepositoryInterface.
 *
 * @method Category[]    findAll()
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface CategoryRepositoryInterface extends AbstractServiceRepositoryInterface
{
}
