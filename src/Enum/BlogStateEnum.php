<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog\Enum;

enum BlogStateEnum: int
{
    case DRAFT = 0;
    case PUBLISHED = 1;
}
