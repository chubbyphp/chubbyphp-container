<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

final class Parameter
{
    /**
     * @param mixed $parameter
     */
    public function __construct(private $parameter) {}

    public function __invoke(): mixed
    {
        return $this->parameter;
    }
}
