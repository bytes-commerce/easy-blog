<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Tests\Integration\Repository;

use BytesCommerce\EasyBlog\Entity\Category;
use BytesCommerce\EasyBlog\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use PHPUnit\Framework\TestCase;

final class CategoryRepositoryTest extends TestCase
{
    private EntityManager $entityManager;
    private CategoryRepository $repository;

    protected function setUp(): void
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../../src/Entity'],
            isDevMode: true,
        );

        $connection = new \Doctrine\DBAL\Connection(
            ['url' => 'sqlite:///:memory:'],
            new Driver()
        );

        $this->entityManager = new EntityManager($connection, $config);
        $this->repository = new CategoryRepository($this->entityManager->getMetadataFactory());

        $this->createSchema();
    }

    private function createSchema(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS category (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                created_at DATETIME,
                updated_at DATETIME,
                seo_title VARCHAR(255) DEFAULT NULL,
                seo_description VARCHAR(255) DEFAULT NULL,
                seo_keywords VARCHAR(255) DEFAULT NULL,
                name LONGTEXT NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description LONGTEXT DEFAULT NULL,
                bottom_description LONGTEXT DEFAULT NULL,
                parent_id INT DEFAULT NULL,
                sort_order INT DEFAULT NULL,
                access_counter INT DEFAULT NULL
            )
        ');
    }

    public function testAddCategory(): void
    {
        $category = new Category();
        $category->setName('Technology');
        $category->setSlug('technology');

        $this->repository->add($category);

        $this->assertNotNull($category->getId());
        $this->assertNotNull($category->getCreatedAt());
    }

    public function testFindCategoryBySlug(): void
    {
        $category = new Category();
        $category->setName('Technology');
        $category->setSlug('technology');

        $this->repository->add($category);

        $found = $this->repository->findOneBy(['slug' => 'technology']);

        $this->assertSame($category->getId(), $found->getId());
        $this->assertEquals('Technology', $found->getName());
    }

    public function testFindAllCategories(): void
    {
        $cat1 = new Category();
        $cat1->setName('Tech');
        $cat1->setSlug('tech');

        $cat2 = new Category();
        $cat2->setName('Science');
        $cat2->setSlug('science');

        $this->repository->add($cat1);
        $this->repository->add($cat2);

        $categories = $this->repository->findAll();

        $this->assertCount(2, $categories);
    }

    public function testDeleteCategory(): void
    {
        $category = new Category();
        $category->setName('To Delete');
        $category->setSlug('to-delete');

        $this->repository->add($category);
        $this->assertNotNull($this->repository->find($category->getId()));

        $this->repository->delete($category);

        $this->assertNull($this->repository->find($category->getId()));
    }

    protected function tearDown(): void
    {
        $this->entityManager->getConnection()->close();
    }
}
