<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\DependencyInjection;

use BytesCommerce\EasyBlog\Repository\CategoryRepository;
use BytesCommerce\EasyBlog\Repository\CategoryRepositoryInterface;
use BytesCommerce\EasyBlog\Repository\FaqRepository;
use BytesCommerce\EasyBlog\Repository\FaqRepositoryInterface;
use BytesCommerce\EasyBlog\Repository\PostRepository;
use BytesCommerce\EasyBlog\Repository\PostRepositoryInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class EasyBlogExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        // Store configuration as parameters (guard against missing optional nodes)
        $container->setParameter('easy_blog.user.entity', $config['user_entity'] ?? 'App\\Entity\\User');
        $container->setParameter('easy_blog.vich_uploader', $config['vich_uploader'] ?? []);
        $container->setParameter('easy_blog.cache', $config['cache'] ?? []);
        $container->setParameter('easy_blog.pagination', $config['pagination'] ?? []);

        // Register repository interfaces with implementations
        $this->registerRepositories($container);

        // Configure Doctrine entity mapping from XML files
        $this->configureXmlMapping($container);
    }

    private function registerRepositories(ContainerBuilder $container): void
    {
        $container->register(CategoryRepositoryInterface::class, CategoryRepository::class)
            ->setPublic(true);

        $container->register(FaqRepositoryInterface::class, FaqRepository::class)
            ->setPublic(true);

        $container->register(PostRepositoryInterface::class, PostRepository::class)
            ->setPublic(true);
    }

    private function configureXmlMapping(ContainerBuilder $container): void
    {
        // Mapping is handled via Doctrine configuration (attributes) in this project
    }

    public function getAlias(): string
    {
        return 'easy_blog';
    }
}
