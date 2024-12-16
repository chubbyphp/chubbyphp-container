<?php

declare(strict_types=1);

namespace Chubbyphp\Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;

final class ExistsException extends \LogicException implements ContainerExceptionInterface
{
    public const TYPE_FACTORY = 'factory';
    public const TYPE_PROTOTYPE_FACTORY = 'prototype factory';

    private function __construct(string $message, int $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $id, string $type): self
    {
        return new self(\sprintf('Factory with id "%s" already exists as "%s"', $id, $type), 2);
    }
}
