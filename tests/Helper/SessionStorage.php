<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Helper;

final class SessionStorage
{
    private array $data = [];

    public function __construct(public readonly string $sessionId) {}

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
