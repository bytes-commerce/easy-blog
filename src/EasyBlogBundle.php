<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog;

use BytesCommerce\EasyBlog\DependencyInjection\Compiler\AddEasyAdminControllersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BytesCommerceEasyBlogBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AddEasyAdminControllersPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
