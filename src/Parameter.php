<?php

declare(strict_types=1);

namespace Chubbyphp\Container;

final class Parameter
{
    /** @var mixed */
    private $parameter;

    /**
     * @param mixed $parameter
     */
    public function __construct($parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return $this->parameter;
    }
}
