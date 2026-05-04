<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

use BytesCommerce\EasyBlog\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use function Symfony\Component\Translation\t;

class CategoryCrudController extends AbstractCrudController
{
    use SeoDataCrudTrait;

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield FormField::addColumn('col-sm-12');
        yield FormField::addFieldset(t('Information'), 'fa fa-info');
        yield TextField::new('name')
            ->setColumns('col-sm-12 col-md-8')
            ->setHelp(t('Name of the category'));

        yield TextField::new('slug')
            ->setColumns('col-sm-12 col-md-2')
            ->setHelp(t('Slug of the category'));

        yield NumberField::new('sort_order')
            ->setColumns('col-sm-12 col-md-2')
            ->setHelp(t('Sort order of the category'));

        yield FormField::addFieldset(t('Content'), 'fa fa-info');
        yield TextEditorField::new('description')
            ->setColumns('col-sm-12')
            ->setHelp(t('Description of the category'));

        foreach ($this->getSeoFields() as $field) {
            yield $field;
        }

        yield TextEditorField::new('bottom_description')
            ->setColumns('col-sm-12')
            ->setHelp(t('Bottom description of the category'));

        yield FormField::addFieldset(t('Relation'), 'fa fa-info');
        yield AssociationField::new('parent')
            ->setColumns('col-sm-12 col-md-6')
            ->setRequired(false);

        yield AssociationField::new('children')
            ->setColumns('col-sm-12 col-md-6')
            ->setRequired(false);

        yield NumberField::new('access_counter', t('Access counter'))
            ->setDisabled()
            ->setColumns('col-sm-12 col-md-6');
    }
}
