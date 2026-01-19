<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Integration;

use Pimple\Container as PimpleContainer;
use Pimple\ServiceProviderInterface;

final class PimpleFooProvider implements ServiceProviderInterface
{
    public function register(PimpleContainer $container): void
    {
        $container['session_storage'] = static fn (PimpleContainer $container): object => new ($container['session_storage_class'])($container['cookie_name']);
    }
}
