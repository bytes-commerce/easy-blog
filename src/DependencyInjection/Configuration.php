<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('easy_blog');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('user_entity')
                    ->defaultValue('App\\Entity\\User')
                    ->info('The host app User entity class that Post references via ManyToMany')
                ->end()
                ->arrayNode('vich_uploader')
                    ->info('Vich uploader configuration for blog images')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('image_path')->defaultValue('/uploads/')->end()
                        ->scalarNode('upload_dir')->defaultValue('public/uploads/')->end()
                    ->end()
                ->end()
                ->arrayNode('cache')
                    ->info('Cache configuration for blog categories')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->integerNode('ttl')->defaultValue(7200)->end()
                    ->end()
                ->end()
                ->arrayNode('pagination')
                    ->info('Pagination settings')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('page_size')->defaultValue(5)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
