<?php

declare(strict_types=1);

namespace Chubbyphp\Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;

final class ContainerException extends \LogicException implements ContainerExceptionInterface
{
    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $id, \Throwable $previous): self
    {
        return new self(sprintf('Could not create service with id "%s"', $id), 1, $previous);
    }
}
