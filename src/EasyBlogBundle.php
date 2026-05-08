<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog;

use BytesCommerce\EasyBlog\DependencyInjection\Compiler\AddEasyAdminControllersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class EasyBlogBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AddEasyAdminControllersPass());
    }

    public function getPath(): string
    {
        // Return the directory of this class (src/) so that conventional paths like
        // "Resources/..." resolve to src/Resources/... as expected by Doctrine/Symfony.
        return __DIR__;
    }
}
