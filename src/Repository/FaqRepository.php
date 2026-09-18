<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Repository;

use BytesCommerce\EasyBlog\Entity\Faq;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractServiceRepository<Faq>
 */
final class FaqRepository extends AbstractServiceRepository implements FaqRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }
}
