# EasyBlog

A drop-in blog bundle for Symfony with EasyAdmin support. Provides a complete blog system with posts, categories, FAQs,
and SEO features.

## Features

- **Posts** - Create and manage blog posts with rich content
- **Categories** - Hierarchical category system with parent/child relationships
- **FAQs** - Associate FAQs with posts
- **SEO** - Built-in SEO fields (title, description, keywords)
- **EasyAdmin Integration** - Full CRUD controllers for EasyAdmin dashboard
- ** Twig Templates** - Responsive default templates

## Requirements

- PHP 8.3+
- Symfony 7.0+ or 8.0+
- Doctrine ORM 3.0+
- EasyAdminBundle 5.0+

## Installation

### 1. Add the bundle via Composer

```bash
composer require bytes-commerce/easy-blog
```

### 2. Register the bundle

In `config/bundles.php`:

```php
return [
    // ...
    BytesCommerce\EasyBlog\EasyBlogBundle::class => ['all' => true],
];
```

### 3. Configure the bundle

Create `config/packages/easy_blog.yaml`:

```yaml
easy_blog:
  user_entity: 'App\Entity\User'  # Your User entity class
```

### 4. Make your User entity implement AuthorAwareInterface

```php
<?php

namespace App\Entity;

use BytesCommerce\EasyBlog\Entity\AuthorAwareInterface;
use BytesCommerce\EasyBlog\Entity\Post;
use Doctrine\Common\Collections\Collection;

class User implements AuthorAwareInterface
{
    // ... existing User code

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
        }
        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);
        return $this;
    }
}
```

### 5. Update your User entity mapping

Add the ManyToMany relationship to your User entity:

```php
#[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'users')]
private Collection $posts;
```

### 6. Clear cache

```bash
php bin/console cache:clear
```

### 7. Add menu items to EasyAdmin Dashboard

In your Dashboard controller:

```php
yield MenuItem::section('Blog', 'fa fa-newspaper');
yield MenuItem::linkToCrud('Categories', 'fa fa-caret-right', Category::class);
yield MenuItem::linkToCrud('Posts', 'fa fa-caret-right', Post::class);
```

## Configuration Reference

```yaml
easy_blog:
  # Required: The host app's User entity that posts reference
  user_entity: 'App\Entity\User'

  # Optional: Vich uploader settings for blog images
  vich_uploader:
    image_path: '/uploads/'
    upload_dir: 'public/uploads/'

  # Optional: Cache configuration
  cache:
    enabled: true
    ttl: 7200  # seconds

  # Optional: Pagination settings
  pagination:
    page_size: 5
```

## Available Routes

| Route                     | Description                                         |
|---------------------------|-----------------------------------------------------|
| `blog.category.all-posts` | List all posts (`/beitraege/{page}`)                |
| `blog.category`           | List posts by category (`/beitraege/{slug}/{page}`) |
| `blog.post`               | View single post (`/beitraege/beitrag/{slug}`)      |
| `blog.ajax.posts`         | AJAX endpoint for post slider                       |

## Template Customization

Override templates by placing them in your project's `templates/bundles/EasyBlog/` directory:

```
templates/
└── bundles/
    └── EasyBlog/
        └── blog/
            ├── base.html.twig
            ├── category/
            │   ├── list.html.twig
            │   └── pagination.html.twig
            └── post/
                ├── card.html.twig
                ├── view.html.twig
                └── ...
```

## Database Schema

The bundle provides Doctrine XML mapping files in `Resources/config/doctrine/`. These are automatically loaded when the
bundle is registered.

### Table Names

All tables use the prefix `bytes_commerce_blog_`:

| Entity          | Table                               |
|-----------------|-------------------------------------|
| Post            | `bytes_commerce_blog_post`           |
| Category        | `bytes_commerce_blog_category`       |
| Faq             | `bytes_commerce_blog_faq`           |
| Post ↔ Category | `bytes_commerce_blog_post_category` |
| Post ↔ User     | Your host app's user table          |

Generate the schema using:

```bash
php bin/console doctrine:schema:create
```

## Testing

```bash
# Run unit tests
./vendor/bin/phpunit --testsuite=Unit

# Run integration tests
./vendor/bin/phpunit --testsuite=Integration

# Run all tests
./vendor/bin/phpunit
```

## Static Analysis

```bash
./vendor/bin/phpstan analyse src/ --level=6
```

## Architecture

The bundle follows Ports & Adapters (Hexagonal) architecture:

```
BytesCommerce\EasyBlog\
├── Controller\          # Symfony controllers
├── Entity\             # Domain entities
├── Enum\               # Value enums
├── Repository\         # Data access interfaces & implementations
└── DependencyInjection\ # Symfony bundle wiring
```

## License

MIT
