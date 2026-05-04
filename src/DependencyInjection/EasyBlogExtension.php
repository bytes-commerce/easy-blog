<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\DependencyInjection;

use BytesCommerce\EasyBlog\Entity\Category;
use BytesCommerce\EasyBlog\Entity\Faq;
use BytesCommerce\EasyBlog\Entity\Post;
use BytesCommerce\EasyBlog\Repository\CategoryRepository;
use BytesCommerce\EasyBlog\Repository\CategoryRepositoryInterface;
use BytesCommerce\EasyBlog\Repository\FaqRepository;
use BytesCommerce\EasyBlog\Repository\FaqRepositoryInterface;
use BytesCommerce\EasyBlog\Repository\PostRepository;
use BytesCommerce\EasyBlog\Repository\PostRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineMappingPass;
use Doctrine\ORM\Mapping\ClassMetadata;
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

        // Store configuration as parameters
        $container->setParameter('easy_blog.user.entity', $config['user_entity']);
        $container->setParameter('easy_blog.vich_uploader', $config['vich_uploader']);
        $container->setParameter('easy_blog.cache', $config['cache']);
        $container->setParameter('easy_blog.pagination', $config['pagination']);

        // Register repository interfaces with implementations
        $this->registerRepositories($container);

        // Configure Doctrine entity mapping
        $this->configureDoctrineMapping($container, $config);
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

    private function configureDoctrineMapping(ContainerBuilder $container, array $config): void
    {
        // Build entity namespaces
        $namespaces = [
            Post::class,
            Category::class,
            Faq::class,
        ];

        // Create a simple mapping pass that registers entity namespaces
        $container->register('easy_blog.doctrine.mapping_pass', DoctrineMappingPass::class)
            ->setArguments([
                array_map(fn($entity) => [$entity], $namespaces),
            ])
            ->addTag('doctrine.container_dql_resolver');
    }

    public function getAlias(): string
    {
        return 'easy_blog';
    }
}
