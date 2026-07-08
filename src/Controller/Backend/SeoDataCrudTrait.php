<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Generator;

trait SeoDataCrudTrait
{
    /**
     * @return Generator<FormField|TextField|TextEditorField>
     */
    public function getSeoFields(): Generator
    {
        yield FormField::addFieldset(self::trans('blog.fieldset.seo'), 'fa fa-search');
        yield TextField::new('seoTitle', self::trans('blog.field.seo_title'))
            ->setColumns('col-sm-12 col-md-6')
            ->hideOnIndex()
            ->setFormTypeOption('attr.maxlength', 120)
            ->setHelp(self::trans('blog.help.seo_title'));
        yield TextField::new('seoKeywords', self::trans('blog.field.seo_keywords'))
            ->setColumns('col-sm-12 col-md-6')
            ->hideOnIndex()
            ->setFormTypeOption('attr.maxlength', 170)
            ->setHelp(self::trans('blog.help.seo_keywords'));
        yield TextEditorField::new('seoDescription', self::trans('blog.field.seo_description'))
            ->setColumns('col-sm-12')
            ->hideOnIndex()
            ->setFormTypeOption('attr.maxlength', 500)
            ->setHelp(self::trans('blog.help.seo_description'));
    }
}
