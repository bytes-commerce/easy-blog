<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as BaseAbstractCrudController;
use Symfony\Component\Translation\TranslatableMessage;
use function Symfony\Component\Translation\t;

abstract class AbstractCrudController extends BaseAbstractCrudController implements CrudControllerInterface
{
    protected static function trans(string $message, array $parameters = []): TranslatableMessage
    {
        return t($message, $parameters, 'EasyBlogBundle');
    }
}
