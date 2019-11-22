<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Unit\Exceptions;

use Chubbyphp\Container\Exceptions\ContainerException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Container\Exceptions\ContainerException
 *
 * @internal
 */
final class ContainerExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                ContainerException::class,
                self::class
            )
        );

        new ContainerException('test', 0);
    }

    public function testCreate(): void
    {
        $previous = new \Exception();

        $exception = ContainerException::create('id', $previous);

        self::assertSame('Could not create service with id "id"', $exception->getMessage());
        self::assertSame(1, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
