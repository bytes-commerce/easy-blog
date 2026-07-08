<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Controller\Backend;

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
use Symfony\Contracts\Translation\TranslatorInterface;

class PostCrudController extends AbstractCrudController
{
    use SeoDataCrudTrait;

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

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
        $crud
            ->setEntityLabelInSingular(self::trans('blog.post.entity.singular'))
            ->setEntityLabelInPlural(self::trans('blog.post.entity.plural'))
            ->setDefaultSort(['created_at' => 'DESC'])
            ->setFormThemes(['@!EasyAdmin/crud/form_theme.html.twig', '@EasyBlog/form_theme_blog.html.twig']);

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();

        yield FormField::addColumn('col-sm-12');
        yield FormField::addFieldset(self::trans('blog.fieldset.information'), 'fa fa-info');
        yield TextField::new('name', self::trans('blog.field.name'))
            ->setColumns('col-sm-12 col-md-6')
            ->setFormTypeOption('attr.maxlength', 255)
            ->setHelp(self::trans('blog.help.name'));

        yield TextField::new('slug', self::trans('blog.field.slug'))
            ->setColumns('col-sm-12 col-md-4')
            ->setFormTypeOption('attr.maxlength', 255)
            ->setFormTypeOption('attr.pattern', '^(?!/).*$')
            ->setFormTypeOption('attr.data-validate-slug', true)
            ->setFormTypeOption(
                'attr.data-validate-slug-message',
                $this->translator->trans('blog.error.slug_leading_slash', [], 'EasyBlogBundle'),
            )
            ->setHelp(self::trans('blog.help.slug'));

        yield ChoiceField::new('status', self::trans('blog.field.status'))
            ->setColumns('col-sm-4 col-md-1')
            ->setChoices([
                $this->translator->trans('blog.status.draft', [], 'EasyBlogBundle') => BlogStateEnum::DRAFT,
                $this->translator->trans('blog.status.published', [], 'EasyBlogBundle') => BlogStateEnum::PUBLISHED,
            ])
            ->setHelp(self::trans('blog.help.status'));

        yield NumberField::new('access_counter', self::trans('blog.field.access_counter'))
            ->setDisabled()
            ->setColumns('col-sm-12 col-md-2')
            ->setHelp(self::trans('blog.help.access_counter'));

        yield FormField::addFieldset(self::trans('blog.fieldset.content'), 'fa fa-pencil');
        yield TextEditorField::new('content', self::trans('blog.field.content'))
            ->hideOnIndex()
            ->setColumns('col-sm-12')
            ->setHelp(self::trans('blog.help.content'));

        foreach ($this->getSeoFields() as $field) {
            yield $field;
        }

        yield FormField::addFieldset(self::trans('blog.fieldset.image'), 'fa fa-image');
        yield ImageField::new('image', self::trans('blog.field.image'))
            ->setColumns('col-sm-12')
            ->setBasePath('/uploads/')
            ->setUploadDir('public/uploads/')
            ->setHelp(self::trans('blog.help.image'));
        yield TextField::new('credits', self::trans('blog.field.credits'))
            ->hideOnIndex()
            ->setColumns('col-sm-12')
            ->setHelp(self::trans('blog.help.credits'));

        yield FormField::addFieldset(self::trans('blog.fieldset.relation'), 'fa fa-info');
        yield AssociationField::new('categories', self::trans('blog.field.categories'))
            ->setColumns('col-sm-12')
            ->hideOnIndex()
            ->setRequired(false)
            ->setHelp(self::trans('blog.help.categories'));

        yield FormField::addFieldset(self::trans('blog.fieldset.faq'), 'fa fa-questionmark');
        yield CollectionField::new('faqs', self::trans('blog.field.faqs'))
            ->hideOnIndex()
            ->useEntryCrudForm(FaqCrudController::class)
            ->setColumns('col-sm-12')
            ->setHelp(self::trans('blog.help.faqs'));
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

        $viewPost = Action::new('viewPost', self::trans('blog.action.view_post'))
            ->linkToRoute('website_blog_article', fn (Post $entity) => ['slug' => $entity->getSlug()])
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
