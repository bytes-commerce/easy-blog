<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Repository;

use BytesCommerce\EasyBlog\Entity\Faq;

/**
 * Interface FaqRepositoryInterface.
 *
 * @method Faq[]    findAll()
 * @method Faq|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faq|null findOneBy(array $criteria, array $orderBy = null)
 * @method Faq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface FaqRepositoryInterface extends AbstractServiceRepositoryInterface
{
}
