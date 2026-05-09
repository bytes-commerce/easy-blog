<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Enum;

enum BlogStateEnum: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
