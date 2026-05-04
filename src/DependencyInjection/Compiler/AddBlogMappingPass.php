<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\DependencyInjection\Compiler;

use BytesCommerce\EasyBlog\Entity\Category;
use BytesCommerce\EasyBlog\Entity\Faq;
use BytesCommerce\EasyBlog\Entity\Post;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * This pass registers the Blog entity namespaces with Doctrine.
 * The actual mapping is done via PHP attributes on the entities.
 */
final class AddBlogMappingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Register entity namespaces with Doctrine bundle
        // This allows Doctrine to discover the entities via attributes
        $container->setParameter('doctrine.orm.entity_namespaces', [
            'BytesCommerce\\EasyBlog\\Entity' => Post::class,
        ]);

        // Add entity classes to Doctrine's entity manager
        $container->setParameter('easy_blog.entity_classes', [
            Post::class,
            Category::class,
            Faq::class,
        ]);
    }
}
