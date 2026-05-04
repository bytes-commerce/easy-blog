<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Repository;

use BytesCommerce\EasyBlog\Entity\TimeAwareInterface;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

abstract class AbstractServiceRepository extends ServiceEntityRepository implements AbstractServiceRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        string $entityClass,
    ) {
        parent::__construct($registry, $entityClass);
    }

    public function add(object $entity, ?EntityManagerInterface $entityManager = null): void
    {
        $em = $entityManager ?? $this->getEntityManager();
        if ($entity instanceof TimeAwareInterface) {
            $this->processTimings($entity);
        }

        $em->persist($entity);
        $em->flush();
    }

    public function delete(object $entity): void
    {
        Assert::true($this->getEntityManager()->contains($entity));
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    private function processTimings(TimeAwareInterface $entity): void
    {
        $time = new DateTimeImmutable();
        if ($entity->getCreatedAt() === null) {
            $entity->setCreatedAt($time);
        }
        $entity->setUpdatedAt($time);
    }
}
