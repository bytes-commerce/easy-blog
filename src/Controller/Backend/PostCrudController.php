<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

use BytesCommerce\EasyBlog\Entity\AuthorAwareInterface;
use BytesCommerce\EasyBlog\Entity\Post;
use BytesCommerce\EasyBlog\Enum\BlogStateEnum;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use DOMXPath;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function Symfony\Component\Translation\t;

class PostCrudController extends AbstractCrudController
{
    use SeoDataCrudTrait;

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function createEntity(string $entityFqcn): object
    {
        $post = new Post();

        return $post;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);
        $crud->setDefaultSort(['created_at' => 'DESC']);

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();

        yield FormField::addColumn('col-sm-12');
        yield FormField::addFieldset(t('Information'), 'fa fa-info');
        yield TextField::new('name')
            ->setColumns('col-sm-12 col-md-6')
            ->setHelp(t('Name of the post'));

        yield TextField::new('slug')
            ->setColumns('col-sm-12 col-md-4')
            ->setHelp(t('Slug of the post'));

        yield ChoiceField::new('status')
            ->setColumns('col-sm-4 col-md-1')
            ->setChoices([
                'Draft' => BlogStateEnum::DRAFT,
                'Published' => BlogStateEnum::PUBLISHED,
            ])
            ->setHelp(t('Status of the post'));

        yield NumberField::new('access_counter', t('Access counter'))
            ->setDisabled()
            ->setColumns('col-sm-12 col-md-2');

        yield FormField::addFieldset(t('Content'), 'fa fa-pencil');
        yield TextEditorField::new('content')
            ->hideOnIndex()
            ->setColumns('col-sm-12')
            ->setHelp(t('The actual blog post comes here...'));

        foreach ($this->getSeoFields() as $field) {
            yield $field;
        }

        yield FormField::addFieldset('Image', 'fa fa-image');
        yield ImageField::new('image')
            ->setColumns('col-sm-12')
            ->setBasePath('/uploads/')
            ->setUploadDir('public/uploads/')
            ->setHelp(t('Image of the post'));
        yield TextField::new('credits', t('Credits'))
            ->hideOnIndex()
            ->setHelp(t('Just paste any HTML code you have from your source, i.e. if the resource is from unsplash or similar. This will be displayed below the image and is mandatory to do in Germany.'));

        yield FormField::addFieldset(t('Relation'), 'fa fa-info');
        yield AssociationField::new('categories')
            ->setColumns('col-sm-12')
            ->hideOnIndex()
            ->setRequired(false);

        yield FormField::addFieldset(t('FAQ'), 'fa fa-questionmark');
        yield CollectionField::new('faqs', 'FAQs')
            ->hideOnIndex()
            ->useEntryCrudForm(FaqCrudController::class)
            ->setColumns('col-sm-12')
            ->setHelp(t('FAQs for this post'));
    }

    /**
     * @param Post $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Post) {
            return;
        }

        $content = $entityInstance->getContent();
        if ($content !== null) {
            $entityInstance->setContent($this->wrapCodeBlockContainersInPre($content));
        }

        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        $viewPost = Action::new('viewPost', 'View Post')
            ->linkToRoute('blog.post', fn (Post $entity) => ['slug' => $entity->getSlug()])
            ->displayIf(fn ($entity) => $entity->getId() !== null)
            ->addCssClass('btn btn-success');
        $actions->add(Crud::PAGE_EDIT, $viewPost);

        return $actions;
    }

    private function wrapCodeBlockContainersInPre(string $html): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        $xpath = new DOMXPath($dom);
        $containers = $xpath->query("//div[contains(@class, 'ql-code-block-container')]");

        foreach ($containers as $container) {
            $pre = $dom->createElement('pre');
            $pre->setAttribute('spellcheck', 'false');

            foreach ($container->attributes as $attr) {
                $pre->setAttribute($attr->nodeName, $attr->nodeValue);
            }

            while ($container->firstChild) {
                $pre->appendChild($container->firstChild);
            }

            $container->parentNode->replaceChild($pre, $container);
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $innerHTML = '';
        foreach ($body->childNodes as $child) {
            $innerHTML .= $dom->saveHTML($child);
        }

        return str_replace('<p><br></p>', '', $innerHTML);
    }
}
