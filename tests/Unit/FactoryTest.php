<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Unit;

use Chubbyphp\Container\Factory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
#[CoversClass(Factory::class)]
final class FactoryTest extends TestCase
{
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

        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['key1'], 'value1'),
            new WithReturn('get', ['key2'], 'value2'),
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
