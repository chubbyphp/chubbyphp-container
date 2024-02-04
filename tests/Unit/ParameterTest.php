<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Unit;

use Chubbyphp\Container\Parameter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Container\Parameter
 *
 * @internal
 */
final class ParameterTest extends TestCase
{
    /**
     * @dataProvider provideInvokeCases
     */
    public function testInvoke(mixed $data): void
    {
        $parameter = new Parameter($data);

        self::assertSame($data, $parameter());
    }

    public static function provideInvokeCases(): iterable
    {
        return [
            'bool' => [
                'data' => true,
            ],
            'int' => [
                'data' => 5,
            ],
            'float' => [
                'data' => 5.5,
            ],
            'string' => [
                'data' => 'test',
            ],
            'array' => [
                'data' => ['key' => 'value'],
            ],
            'object' => [
                'data' => new \stdClass(),
            ],
        ];
    }
}
