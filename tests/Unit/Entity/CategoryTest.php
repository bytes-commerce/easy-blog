<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Tests\Unit\Entity;

use BytesCommerce\EasyBlog\Entity\Category;
use BytesCommerce\EasyBlog\Entity\Post;
use PHPUnit\Framework\TestCase;

final class CategoryTest extends TestCase
{
    public function testCategoryCreation(): void
    {
        $category = new Category();
        $category->setName('Technology');
        $category->setSlug('technology');

        $this->assertEquals('Technology', $category->getName());
        $this->assertEquals('technology', $category->getSlug());
    }

    public function testCategoryToString(): void
    {
        $category = new Category();
        $category->setName('Tech');

        $this->assertEquals('Tech', (string) $category);
    }

    public function testCategoryDescription(): void
    {
        $category = new Category();
        $category->setDescription('<p>This is a tech category</p>');
        $category->setBottomDescription('<p>More info at the bottom</p>');

        $this->assertEquals('<p>This is a tech category</p>', $category->getDescription());
        $this->assertEquals('<p>More info at the bottom</p>', $category->getBottomDescription());
    }

    public function testCategorySortOrder(): void
    {
        $category = new Category();
        $category->setSortOrder(5);

        $this->assertEquals(5, $category->getSortOrder());
    }

    public function testCategoryAccessCounter(): void
    {
        $category = new Category();
        $category->setAccessCounter(250);

        $this->assertEquals(250, $category->getAccessCounter());
    }

    public function testCategorySeoData(): void
    {
        $category = new Category();
        $category->setSeoTitle('Tech SEO Title');
        $category->setSeoDescription('Tech SEO Description');
        $category->setSeoKeywords('tech, technology, gadgets');

        $this->assertEquals('Tech SEO Title', $category->getSeoTitle());
        $this->assertEquals('Tech SEO Description', $category->getSeoDescription());
        $this->assertEquals('tech, technology, gadgets', $category->getSeoKeywords());
    }

    public function testCategoryParentChildRelation(): void
    {
        $parent = new Category();
        $parent->setName('Parent Category');
        $parent->setSlug('parent-category');

        $child = new Category();
        $child->setName('Child Category');
        $child->setSlug('child-category');

        $parent->addChild($child);

        $this->assertSame($parent, $child->getParent());
        $this->assertCount(1, $parent->getChildren());
        $this->assertTrue($parent->getChildren()->contains($child));
    }

    public function testRemoveChild(): void
    {
        $parent = new Category();
        $parent->setName('Parent Category');
        $parent->setSlug('parent-category');

        $child = new Category();
        $child->setName('Child Category');
        $child->setSlug('child-category');

        $parent->addChild($child);
        $this->assertCount(1, $parent->getChildren());

        $parent->removeChild($child);
        $this->assertCount(0, $parent->getChildren());
        $this->assertNull($child->getParent());
    }

    public function testCategoryPostRelation(): void
    {
        $category = new Category();
        $category->setName('Tech');
        $category->setSlug('tech');

        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setContent('Content');

        $category->addPost($post);

        $this->assertCount(1, $category->getPosts());
        $this->assertTrue($category->getPosts()->contains($post));
    }

    public function testRemovePost(): void
    {
        $category = new Category();
        $category->setName('Tech');
        $category->setSlug('tech');

        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setContent('Content');

        $category->addPost($post);
        $this->assertCount(1, $category->getPosts());

        $category->removePost($post);
        $this->assertCount(0, $category->getPosts());
    }
}
