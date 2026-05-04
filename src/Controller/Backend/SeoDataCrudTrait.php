<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use function Symfony\Component\Translation\t;

trait SeoDataCrudTrait
{
    public function getSeoFields(): iterable
    {
        yield FormField::addFieldset(t('SEO'), 'fa fa-search');
        yield TextField::new('seoTitle')
            ->setColumns('col-sm-12 col-md-6')
            ->hideOnIndex()
            ->setHelp(t('SEO Title'));
        yield TextField::new('seoKeywords')
            ->setColumns('col-sm-12 col-md-6')
            ->hideOnIndex()
            ->setHelp(t('SEO Keywords'));
        yield TextEditorField::new('seoDescription')
            ->setColumns('col-sm-12')
            ->hideOnIndex()
            ->setHelp(t('SEO Description'));
    }
}
