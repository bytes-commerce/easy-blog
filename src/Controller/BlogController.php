<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller;

use BytesCommerce\EasyBlog\Entity\Category;
use BytesCommerce\EasyBlog\Enum\BlogStateEnum;
use BytesCommerce\EasyBlog\Repository\CategoryRepositoryInterface;
use BytesCommerce\EasyBlog\Repository\PostRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use DOMDocument;
use DOMXPath;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/')]
class BlogController extends AbstractController
{
    public const PAGE_SIZE = 5;

    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly RequestStack $requestStack,
        private readonly CacheInterface $cache,
    ) {
    }

    #[Route('/beitraege/{page}', name: 'blog.category.all-posts', requirements: [
        'page' => '\d+',
    ], methods: ['GET'])]
    public function showAllPostsAction(?int $page = 1): Response
    {
        $posts = $this->postRepository->findBy(['status' => BlogStateEnum::PUBLISHED], ['created_at' => 'DESC']);
        $page = max(0, $page - 1);
        $pages = array_chunk($posts, self::PAGE_SIZE);

        return $this->render('@EasyBlog/blog/category/list.html.twig', [
            'posts' => $pages[$page] ?? [],
            'currentPage' => $page + 1,
            'pages' => count($pages),
            'category' => null,
        ]);
    }

    #[Route('/beitraege/{slug}/{page}', name: 'blog.category', requirements: [
        'page' => '\d+',
        'slug' => '[a-z0-9-]+',
    ], methods: ['GET'])]
    public function showCategory(?string $slug = null, int $page = 1): Response
    {
        if ($slug !== null) {
            $category = $this->categoryRepository->findOneBy(['slug' => $slug]);
            if ($category === null) {
                $this->addFlash(
                    'danger',
                    'Scheinbar haben wir diese Kategorie entfernt. Hier finden Sie alle unsere Meldungen.',
                );

                return $this->redirectToRoute('blog.category.all-posts');
            }

            $allPosts = $this->postRepository->findPostsByCategory($category);
            $page = max(0, $page - 1);
            $posts = $allPosts[$page] ?? [];

            return $this->render('@EasyBlog/blog/category/list.html.twig', [
                'currentPage' => $page + 1,
                'posts' => $posts,
                'pages' => count($allPosts),
                'category' => $category,
            ]);
        }

        return $this->redirectToRoute('blog.category.all-posts');
    }

    #[Route('/beitraege/beitrag/{slug}', name: 'blog.post', methods: ['GET'])]
    public function showPost(?string $slug = null): Response
    {
        if ($slug !== null) {
            $post = $this->postRepository->findOneBy(['slug' => $slug, 'status' => BlogStateEnum::PUBLISHED]);
            if ($post === null) {
                $this->addFlash(
                    'danger',
                    'Scheinbar haben wir diesen Post entfernt. Hier finden Sie alle unsere Meldungen.',
                );

                return $this->redirectToRoute('blog.category.all-posts');
            }

            $contentPayload = $this->createHeadingIndexAndModifyHTML($post->getContent());
            $post->setContent($contentPayload->get('content'));

            return $this->render('@EasyBlog/blog/post/view.html.twig', [
                'post' => $post,
                'index' => $contentPayload->get('index'),
            ]);
        }

        return $this->redirectToRoute('blog.category.all-posts');
    }

    #[Route('/frontend-api/blog/posts', name: 'blog.ajax.posts', defaults: ['exclude_in_sitemap' => true], methods: ['GET'])]
    public function showPostSliderViaAjaxAction(): Response
    {
        return $this->json([
            'status' => 'success',
            'message' => $this->renderView('@EasyBlog/blog/post/slider-wrapper.html.twig', [
                'posts' => $this->postRepository->findBy(['status' => BlogStateEnum::PUBLISHED], ['created_at' => 'DESC'], 10),
            ])
        ]);
    }

    private function slugify(string $string): string
    {
        return trim(preg_replace('/[^a-z0-9]/', '-', strtolower($string)), '-');
    }

    private function createHeadingIndexAndModifyHTML(string $html): ArrayCollection
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);
        $headings = $xpath->query('//h1 | //h2 | //h3');
        $index = [];
        $usedSlugs = [];
        foreach ($headings as $heading) {
            $title = trim($heading->textContent);
            $slug = $this->slugify($title);

            if (isset($usedSlugs[$slug])) {
                ++$usedSlugs[$slug];
                $slug .= '-' . $usedSlugs[$slug];
            } else {
                $usedSlugs[$slug] = 1;
            }

            /** @phpstan-ignore-next-line */
            $heading->setAttribute('id', $slug);
            $index[] = [
                'title' => $title,
                'targetAnchor' => $slug,
                'tag' => $heading->nodeName
            ];
        }

        return new ArrayCollection([
            'content' => str_replace(
                ['<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">', '<html>', '</html>', '<body>', '</body>'],
                '',
                $dom->saveHTML(),
            ),
            'index' => $index
        ]);
    }

    protected function getCategories(): array
    {
        return $this->cache->get('blog_categories', function (ItemInterface $item): array {
            $item->expiresAfter(7200);

            return $this->categoryRepository->findAll();
        });
    }
}
