<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Unit\Exceptions;

use Chubbyphp\Container\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Container\Exceptions\NotFoundException
 *
 * @internal
 */
final class NotFoundExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                NotFoundException::class,
                self::class
            )
        );

        new NotFoundException('test', 0);
    }

    public function testCreate(): void
    {
        $exception = NotFoundException::create('id');

        self::assertSame('There is no service with id "id"', $exception->getMessage());
        self::assertSame(3, $exception->getCode());
    }
}
