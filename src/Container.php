<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

use Chubbyphp\Container\Exceptions\ContainerException;
use Chubbyphp\Container\Exceptions\ExistsException;
use Chubbyphp\Container\Exceptions\NotFoundException;

final class Container implements ContainerInterface
{
    /**
     * @var array<string, callable>
     */
    private array $factories = [];

    /**
     * @var array<string, callable>
     */
    private array $prototypeFactories = [];

    /**
     * @var array<string, mixed>
     */
    private array $services = [];

    /**
     * @param array<string, callable> $factories
     */
    public function __construct(array $factories = [])
    {
        $this->factories($factories);
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

        if (isset($this->factories[$id])) {
            $factory = new Factory($this->factories[$id], $factory);
        }

        unset($this->services[$id]);

        $this->factories[$id] = $factory;

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
        if (isset($this->factories[$id])) {
            throw ExistsException::create($id, ExistsException::TYPE_FACTORY);
        }

        if (isset($this->prototypeFactories[$id])) {
            $factory = new Factory($this->prototypeFactories[$id], $factory);
        }

        $this->prototypeFactories[$id] = $factory;

        return $this;
    }

    /**
     * @return mixed
     */
    public function get(string $id)
    {
        if (isset($this->prototypeFactories[$id])) {
            return $this->createFromPrototypeFactory($id);
        }

        return $this->services[$id] ?? $this->services[$id] = $this->createFromFactory($id);
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]) || isset($this->prototypeFactories[$id]);
    }

    /**
     * @return mixed
     */
    private function createFromFactory(string $id)
    {
        if (!isset($this->factories[$id])) {
            throw NotFoundException::create($id);
        }

        try {
            return ($this->factories[$id])($this);
        } catch (\Throwable $throwable) {
            throw ContainerException::create($id, $throwable);
        }
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
