<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

interface AbstractServiceRepositoryInterface extends ObjectRepository
{
    public function add(object $entity, ?EntityManagerInterface $entityManager = null): void;

    public function delete(object $entity): void;

    /**
     * @param array<string, mixed> $criteria
     */
    public function count(array $criteria): int;
}
