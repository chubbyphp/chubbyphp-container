<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

use Chubbyphp\Container\Exceptions\ContainerException;
use Chubbyphp\Container\Exceptions\ExistsException;

final class Container implements ContainerInterface
{
    /**
     * @var ContainerInterface
     */
    private $minimalContainer;

    /**
     * @var array<string, callable>
     */
    private $prototypeFactories = [];

    /**
     * @param array<string, callable> $factories
     */
    public function __construct(array $factories = [])
    {
        $this->minimalContainer = new MinimalContainer($factories);
    }

    /**
     * @param array<string, callable> $factories
     */
    public function factories(array $factories): ContainerInterface
    {
        foreach ($factories as $id => $factory) {
            $this->factory($id, $factory);
        }

        return $this;
    }

    public function factory(string $id, callable $factory): ContainerInterface
    {
        if (isset($this->prototypeFactories[$id])) {
            throw ExistsException::create($id, ExistsException::TYPE_PROTOTYPE_FACTORY);
        }

        $this->minimalContainer->factory($id, $factory);

        return $this;
    }

    /**
     * @param array<string, callable> $factories
     */
    public function prototypeFactories(array $factories): ContainerInterface
    {
        foreach ($factories as $id => $factory) {
            $this->prototypeFactory($id, $factory);
        }

        return $this;
    }

    public function prototypeFactory(string $id, callable $factory): ContainerInterface
    {
        if ($this->minimalContainer->has($id)) {
            throw ExistsException::create($id, ExistsException::TYPE_FACTORY);
        }

        if (isset($this->prototypeFactories[$id])) {
            $factory = new Factory($this->prototypeFactories[$id], $factory);
        }

        $this->prototypeFactories[$id] = $factory;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        if (isset($this->prototypeFactories[$id])) {
            return $this->createFromPrototypeFactory($id);
        }

        return $this->minimalContainer->get($id);
    }

    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return isset($this->prototypeFactories[$id]) || $this->minimalContainer->has($id);
    }

    /**
     * @return mixed
     */
    private function createFromPrototypeFactory(string $id)
    {
        try {
            return ($this->prototypeFactories[$id])($this);
        } catch (\Throwable $throwable) {
            throw ContainerException::create($id, $throwable);
        }
    }
}
