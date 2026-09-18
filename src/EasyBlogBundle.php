<?php

declare(strict_types=1);

namespace BytesCommerce\EasyBlog;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class EasyBlogBundle extends Bundle
{
    public function getPath(): string
    {
        return __DIR__;
    }
}
