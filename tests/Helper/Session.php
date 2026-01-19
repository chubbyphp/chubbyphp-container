<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Helper;

final class Session
{
    public function __construct(public readonly SessionStorage $session) {}
}
