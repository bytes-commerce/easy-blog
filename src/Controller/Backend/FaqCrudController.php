<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

use BytesCommerce\EasyBlog\Entity\Faq;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use function Symfony\Component\Translation\t;

class FaqCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Faq::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();

        yield FormField::addColumn('col-sm-12');
        yield FormField::addFieldset(t('Questions and answers'), 'fa fa-info');
        yield TextField::new('question')
            ->setColumns('col-sm-12')
            ->setHelp(t('The question of the FAQ'));

        yield TextEditorField::new('answer')
            ->setColumns('col-sm-12')
            ->setHelp(t('The meaningful answer of this FAQ'));
    }
}
