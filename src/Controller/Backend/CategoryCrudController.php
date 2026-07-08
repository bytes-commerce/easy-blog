<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

use BytesCommerce\EasyBlog\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryCrudController extends AbstractCrudController
{
    use SeoDataCrudTrait;

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular(self::trans('blog.category.entity.singular'))
            ->setEntityLabelInPlural(self::trans('blog.category.entity.plural'))
            ->setFormThemes(['@!EasyAdmin/crud/form_theme.html.twig', '@EasyBlog/form_theme_blog.html.twig']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield FormField::addColumn('col-sm-12');
        yield FormField::addFieldset(self::trans('blog.fieldset.information'), 'fa fa-info');
        yield TextField::new('name', self::trans('blog.field.name'))
            ->setColumns('col-sm-12 col-md-8')
            ->setFormTypeOption('attr.maxlength', 255)
            ->setHelp(self::trans('blog.help.name'));

        yield TextField::new('slug', self::trans('blog.field.slug'))
            ->setColumns('col-sm-12 col-md-2')
            ->setFormTypeOption('attr.maxlength', 255)
            ->setFormTypeOption('attr.pattern', '^(?!/).*$')
            ->setFormTypeOption('attr.data-validate-slug', true)
            ->setFormTypeOption(
                'attr.data-validate-slug-message',
                $this->translator->trans('blog.error.slug_leading_slash', [], 'EasyBlogBundle'),
            )
            ->setHelp(self::trans('blog.help.slug'));

        yield NumberField::new('sort_order', self::trans('blog.field.sort_order'))
            ->setColumns('col-sm-12 col-md-2')
            ->setHelp(self::trans('blog.help.sort_order'));

        yield FormField::addFieldset(self::trans('blog.fieldset.content'), 'fa fa-info');
        yield TextEditorField::new('description', self::trans('blog.field.description'))
            ->setColumns('col-sm-12')
            ->setHelp(self::trans('blog.help.description'));

        foreach ($this->getSeoFields() as $field) {
            yield $field;
        }

        yield TextEditorField::new('bottom_description', self::trans('blog.field.bottom_description'))
            ->setColumns('col-sm-12')
            ->setHelp(self::trans('blog.help.bottom_description'));

        yield FormField::addFieldset(self::trans('blog.fieldset.relation'), 'fa fa-info');
        yield AssociationField::new('parent', self::trans('blog.field.parent'))
            ->setColumns('col-sm-12 col-md-6')
            ->setRequired(false)
            ->setHelp(self::trans('blog.help.parent'));

        yield AssociationField::new('children', self::trans('blog.field.children'))
            ->setColumns('col-sm-12 col-md-6')
            ->setRequired(false)
            ->setHelp(self::trans('blog.help.children'));

        yield NumberField::new('access_counter', self::trans('blog.field.access_counter'))
            ->setDisabled()
            ->setColumns('col-sm-12 col-md-6')
            ->setHelp(self::trans('blog.help.access_counter'));
    }
}
