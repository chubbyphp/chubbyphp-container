<?php

declare(strict_types=1);

namespace Chubbyphp\Container\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

final class NotFoundException extends \LogicException implements NotFoundExceptionInterface
{
    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $id): self
    {
        return new self(sprintf('There is no service with id "%s"', $id), 3);
    }
}
