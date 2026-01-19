<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Helper;

use Chubbyphp\Container\Container as ChubbyphpContainer;

final class ChubbyphpFooProvider
{
    public function __invoke(): array
    {
        return [
            'session_storage' => static fn (ChubbyphpContainer $container): object => new ($container->get('session_storage_class'))($container->get('cookie_name')),
        ];
    }
}
