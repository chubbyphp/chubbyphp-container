<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

final class Parameter
{
    /**
     * @param mixed $parameter
     */
    public function __construct(private $parameter) {}

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return $this->parameter;
    }
}
