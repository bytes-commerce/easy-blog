<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Tests\Unit\Entity;

use BytesCommerce\EasyBlog\Entity\Category;
use BytesCommerce\EasyBlog\Entity\Faq;
use BytesCommerce\EasyBlog\Entity\Post;
use BytesCommerce\EasyBlog\Enum\BlogStateEnum;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function testPostCreation(): void
    {
        $post = new Post();
        $post->setName('Test Post');
        $post->setSlug('test-post');
        $post->setContent('<p>Test content</p>');

        $this->assertEquals('Test Post', $post->getName());
        $this->assertEquals('test-post', $post->getSlug());
        $this->assertEquals('<p>Test content</p>', $post->getContent());
        $this->assertEquals(BlogStateEnum::DRAFT, $post->getStatus());
    }

    public function testPostStatus(): void
    {
        $post = new Post();
        $this->assertEquals(BlogStateEnum::DRAFT, $post->getStatus());

        $post->setStatus(BlogStateEnum::PUBLISHED);
        $this->assertEquals(BlogStateEnum::PUBLISHED, $post->getStatus());
    }

    public function testPostCategoryRelation(): void
    {
        $post = new Post();
        $category = new Category();
        $category->setName('Tech');
        $category->setSlug('tech');

        $post->addCategory($category);

        $this->assertCount(1, $post->getCategories());
        $this->assertTrue($post->getCategories()->contains($category));
    }

    public function testPostFaqRelation(): void
    {
        $post = new Post();
        $faq = new Faq();
        $faq->setQuestion('What is this?');
        $faq->setAnswer('This is a test FAQ.');

        $post->addFaq($faq);

        $this->assertCount(1, $post->getFaqs());
        $this->assertTrue($post->getFaqs()->contains($faq));
        $this->assertSame($post, $faq->getPost());
    }

    public function testPostSeoData(): void
    {
        $post = new Post();
        $post->setSeoTitle('SEO Title');
        $post->setSeoDescription('SEO Description');
        $post->setSeoKeywords('keyword1, keyword2');

        $this->assertEquals('SEO Title', $post->getSeoTitle());
        $this->assertEquals('SEO Description', $post->getSeoDescription());
        $this->assertEquals('keyword1, keyword2', $post->getSeoKeywords());
    }

    public function testPostImage(): void
    {
        $post = new Post();
        $post->setImage('blog/post-image.jpg');
        $post->setCredits('<a href="https://example.com">Image Source</a>');

        $this->assertEquals('blog/post-image.jpg', $post->getImage());
        $this->assertEquals('<a href="https://example.com">Image Source</a>', $post->getCredits());
    }

    public function testPostExcerpt(): void
    {
        $post = new Post();
        $post->setExcerpt('This is a short excerpt of the post.');

        $this->assertEquals('This is a short excerpt of the post.', $post->getExcerpt());
    }

    public function testPostAccessCounter(): void
    {
        $post = new Post();
        $post->setAccessCounter(100);

        $this->assertEquals(100, $post->getAccessCounter());
    }

    public function testRemoveCategory(): void
    {
        $post = new Post();
        $category = new Category();
        $category->setName('Tech');
        $category->setSlug('tech');

        $post->addCategory($category);
        $this->assertCount(1, $post->getCategories());

        $post->removeCategory($category);
        $this->assertCount(0, $post->getCategories());
    }

    public function testRemoveFaq(): void
    {
        $post = new Post();
        $faq = new Faq();
        $faq->setQuestion('What is this?');
        $faq->setAnswer('This is a test FAQ.');

        $post->addFaq($faq);
        $this->assertCount(1, $post->getFaqs());
        $this->assertSame($post, $faq->getPost());

        $post->removeFaq($faq);
        $this->assertCount(0, $post->getFaqs());
        $this->assertNull($faq->getPost());
    }
}
