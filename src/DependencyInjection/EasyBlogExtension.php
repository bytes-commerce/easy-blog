<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\DependencyInjection;

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

        $container->setParameter('easy_blog.user.entity', $config['user_entity'] ?? 'App\\Entity\\User');
        $container->setParameter('easy_blog.vich_uploader', $config['vich_uploader'] ?? []);
        $container->setParameter('easy_blog.cache', $config['cache'] ?? []);
        $container->setParameter('easy_blog.pagination', $config['pagination'] ?? []);
    }

    public function getAlias(): string
    {
        return 'easy_blog';
    }
}
