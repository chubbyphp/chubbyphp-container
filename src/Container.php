<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

use Chubbyphp\Container\Exceptions\ContainerException;
use Chubbyphp\Container\Exceptions\NotFoundException;

final class Container implements ContainerInterface
{
    /**
     * @var array<string, callable>
     */
    private $factories = [];

    /**
     * @var array<string, mixed>
     */
    private $services = [];

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
        if (isset($this->factories[$id])) {
            $factory = new Factory($this->factories[$id], $factory);
        }

        unset($this->services[$id]);

        $this->factories[$id] = $factory;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        return $this->services[$id] ?? $this->services[$id] = $this->create($id);
    }

    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return isset($this->factories[$id]);
    }

    /**
     * @return mixed
     */
    private function create(string $id)
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
}
