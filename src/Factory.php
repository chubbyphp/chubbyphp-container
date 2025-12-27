<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

use Psr\Container\ContainerInterface;

final class Factory
{
    /**
     * @var callable
     */
    private readonly mixed $previousFactory;

    /**
     * @var callable
     */
    private readonly mixed $factory;

    public function __construct(callable $previousFactory, callable $factory)
    {
        $this->previousFactory = $previousFactory;
        $this->factory = $factory;
    }

    public function __invoke(ContainerInterface $container): mixed
    {
        return ($this->factory)($container, $this->previousFactory);
    }
}
