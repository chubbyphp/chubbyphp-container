<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Unit\Exceptions;

use Chubbyphp\Container\Exceptions\ExistsException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Container\Exceptions\ExistsException
 *
 * @internal
 */
final class ExistsExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to private %s::__construct() from context \'%s\'',
                ExistsException::class,
                self::class
            )
        );

        new ExistsException('test', 0);
    }

    /**
     * @dataProvider typeProvider
     */
    public function testCreate(string $type): void
    {
        $exception = ExistsException::create('id', $type);

        self::assertSame(sprintf('Factory with id "id" already exists as "%s"', $type), $exception->getMessage());
        self::assertSame(2, $exception->getCode());
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function typeProvider(): array
    {
        return [
            ExistsException::TYPE_FACTORY => ['type' => ExistsException::TYPE_FACTORY],
            ExistsException::TYPE_PROTOTYPE_FACTORY => ['type' => ExistsException::TYPE_PROTOTYPE_FACTORY],
        ];
    }
}
