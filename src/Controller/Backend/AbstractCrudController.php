<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as BaseAbstractCrudController;

abstract class AbstractCrudController extends BaseAbstractCrudController implements CrudControllerInterface
{
    public function configureAssets(Assets $assets): Assets
    {
        $assets = parent::configureAssets($assets);
        $assets->addAssetMapperEntry('quill-admin');

        return $assets;
    }
}
