<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * @method ContainerInterface prototypeFactories(array $factories)
 * @method ContainerInterface prototypeFactory(string $id, callable $factory)
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * @param array<string, callable> $factories
     */
    public function factories(array $factories): self;

    public function factory(string $id, callable $factory): self;

    /**
     * @param array<string, callable> $factories
     */
    // public function prototypeFactories(array $factories): ContainerInterface;

    // public function prototypeFactory(string $id, callable $factory): ContainerInterface;
}
