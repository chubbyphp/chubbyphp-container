<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

use Psr\Container\ContainerInterface;

final class Factory
{
    /**
     * @var callable
     */
    private $previousFactory;

    /**
     * @var callable
     */
    private $factory;

    public function __construct(callable $previousFactory, callable $factory)
    {
        $this->previousFactory = $previousFactory;
        $this->factory = $factory;
    }

    /**
     * @return mixed
     */
    public function __invoke(ContainerInterface $container)
    {
        return ($this->factory)($container, $this->previousFactory);
    }
}
