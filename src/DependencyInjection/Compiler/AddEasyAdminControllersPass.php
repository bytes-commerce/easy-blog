<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\DependencyInjection\Compiler;

use BytesCommerce\EasyBlog\Controller\Backend\CategoryCrudController;
use BytesCommerce\EasyBlog\Controller\Backend\FaqCrudController;
use BytesCommerce\EasyBlog\Controller\Backend\PostCrudController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * This pass auto-registers EasyAdmin CRUD controllers with the EasyAdmin bundle.
 */
final class AddEasyAdminControllersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Register CRUD controllers as services with easyadmin.controller tag
        $taggedServices = [
            PostCrudController::class,
            CategoryCrudController::class,
            FaqCrudController::class,
        ];

        foreach ($taggedServices as $controllerClass) {
            if ($container->has($controllerClass)) {
                $definition = $container->findDefinition($controllerClass);
                $definition->addTag('easyadmin.controller');
            }
        }
    }
}
