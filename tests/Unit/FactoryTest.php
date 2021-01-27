<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Unit;

use Chubbyphp\Container\Factory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Container\Factory
 *
 * @internal
 */
final class FactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        $existingFactory = static function (ContainerInterface $container): \stdClass {
            $object = new \stdClass();
            $object->key1 = $container->get('key1');

            return $object;
        };

        $factory = static function (ContainerInterface $container, callable $previous): \stdClass {
            $object = $previous($container);
            $object->key2 = $container->get('key2');

            return $object;
        };

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('key1')->willReturn('value1'),
            Call::create('get')->with('key2')->willReturn('value2'),
        ]);

        $extendedFactory = new Factory($existingFactory, $factory);

        self::assertIsCallable($extendedFactory);

        $service = $extendedFactory($container);

        self::assertInstanceOf(\stdClass::class, $service);

        self::assertSame('value1', $service->key1);
        self::assertSame('value2', $service->key2);
    }

    public function testIsLazy(): void
    {
        $existingFactory = static function (ContainerInterface $container): \stdClass {
            $object = new \stdClass();
            $object->key1 = $container->get('key1');

            return $object;
        };

        $factory = static function (ContainerInterface $container, callable $previous): \stdClass {
            $object = $previous($container);
            $object->key2 = $container->get('key2');

            return $object;
        };

        $extendedFactory = new Factory($existingFactory, $factory);

        self::assertIsCallable($extendedFactory);
    }
}
