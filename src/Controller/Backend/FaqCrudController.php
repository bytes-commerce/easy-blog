<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

use BytesCommerce\EasyBlog\Entity\Faq;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<Faq>
 */
class FaqCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Faq::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular(self::trans('blog.faq.entity.singular'))
            ->setEntityLabelInPlural(self::trans('blog.faq.entity.plural'))
            ->setFormThemes(['@!EasyAdmin/crud/form_theme.html.twig', '@EasyBlog/form_theme_blog.html.twig']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();

        yield FormField::addColumn('col-sm-12');
        yield FormField::addFieldset(self::trans('blog.fieldset.faq'), 'fa fa-info');
        yield TextField::new('question', self::trans('blog.field.question'))
            ->setColumns('col-sm-12')
            ->setFormTypeOption('attr.maxlength', 255)
            ->setHelp(self::trans('blog.help.question'));

        yield TextEditorField::new('answer', self::trans('blog.field.answer'))
            ->setColumns('col-sm-12')
            ->setHelp(self::trans('blog.help.answer'));
    }
}
