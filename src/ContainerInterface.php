<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * @param array<string, callable> $factories
     */
    public function factories(array $factories): self;

    public function factory(string $id, callable $factory): self;
}
