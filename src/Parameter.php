<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

final class Parameter
{
    public function __construct(private readonly mixed $parameter) {}

    public function __invoke(): mixed
    {
        return $this->parameter;
    }
}
