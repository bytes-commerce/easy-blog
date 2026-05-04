<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Tests\Integration\Repository;

use BytesCommerce\EasyBlog\Entity\Category;
use BytesCommerce\EasyBlog\Entity\Post;
use BytesCommerce\EasyBlog\Enum\BlogStateEnum;
use BytesCommerce\EasyBlog\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\SQLiteAdapter;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use PHPUnit\Framework\TestCase;

final class PostRepositoryTest extends TestCase
{
    private EntityManager $entityManager;
    private PostRepository $repository;

    protected function setUp(): void
    {
        // Create a simple in-memory SQLite database for testing
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../../src/Entity'],
            isDevMode: true,
        );

        $connection = new \Doctrine\DBAL\Connection(
            ['url' => 'sqlite:///:memory:'],
            new Driver()
        );

        $this->entityManager = new EntityManager($connection, $config);
        $this->repository = new PostRepository($this->entityManager->getMetadataFactory());

        // Create schema
        $this->createSchema();
    }

    private function createSchema(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS post (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                created_at DATETIME,
                updated_at DATETIME,
                seo_title VARCHAR(255) DEFAULT NULL,
                seo_description VARCHAR(255) DEFAULT NULL,
                seo_keywords VARCHAR(255) DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                content LONGTEXT NOT NULL,
                image VARCHAR(255) DEFAULT NULL,
                excerpt LONGTEXT DEFAULT NULL,
                credits LONGTEXT DEFAULT NULL,
                access_counter INT DEFAULT NULL,
                status INT NOT NULL
            )
        ');

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

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS blog_post_category (
                post_id INTEGER NOT NULL,
                category_id INTEGER NOT NULL,
                PRIMARY KEY(post_id, category_id)
            )
        ');
    }

    public function testAddPost(): void
    {
        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setContent('Test content');
        $post->setStatus(BlogStateEnum::PUBLISHED);

        $this->repository->add($post);

        $this->assertNotNull($post->getId());
        $this->assertNotNull($post->getCreatedAt());
        $this->assertNotNull($post->getUpdatedAt());
    }

    public function testFindPostById(): void
    {
        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setContent('Test content');
        $post->setStatus(BlogStateEnum::PUBLISHED);

        $this->repository->add($post);

        $found = $this->repository->find($post->getId());

        $this->assertSame($post->getId(), $found->getId());
        $this->assertEquals('Test Post', $found->getName());
    }

    public function testFindPublishedPosts(): void
    {
        $published = new Post();
        $published->setName('Published Post');
        $published->setSlug('published-post');
        $published->setContent('Published content');
        $published->setStatus(BlogStateEnum::PUBLISHED);

        $draft = new Post();
        $draft->setName('Draft Post');
        $draft->setSlug('draft-post');
        $draft->setContent('Draft content');
        $draft->setStatus(BlogStateEnum::DRAFT);

        $this->repository->add($published);
        $this->repository->add($draft);

        $publishedPosts = $this->repository->findBy(['status' => BlogStateEnum::PUBLISHED]);

        $this->assertCount(1, $publishedPosts);
        $this->assertEquals('Published Post', $publishedPosts[0]->getName());
    }

    public function testDeletePost(): void
    {
        $post = new Post();
        $post->setName('To Delete');
        $post->setSlug('to-delete');
        $post->setContent('Content');
        $post->setStatus(BlogStateEnum::DRAFT);

        $this->repository->add($post);
        $this->assertNotNull($this->repository->find($post->getId()));

        $this->repository->delete($post);

        $this->assertNull($this->repository->find($post->getId()));
    }

    protected function tearDown(): void
    {
        $this->entityManager->getConnection()->close();
    }
}
