<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Unit;

use Chubbyphp\Container\Container;
use Chubbyphp\Container\Exceptions\ContainerException;
use Chubbyphp\Container\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Container\Container
 *
 * @internal
 */
final class ContainerTest extends TestCase
{
    public function testConstruct(): void
    {
        $container = new Container([
            'id' => static function () {
                return new \stdClass();
            },
        ]);

        $reflectionFactories = new \ReflectionProperty($container, 'factories');
        $reflectionFactories->setAccessible(true);

        $factories = $reflectionFactories->getValue($container);

        self::assertCount(1, $factories);

        $factory = array_shift($factories);

        $service = $factory($container);

        self::assertInstanceOf(\stdClass::class, $service);
    }

    public function testFactories(): void
    {
        $container = new Container();
        $container->factories([
            'id' => static function () {
                return new \stdClass();
            },
        ]);

        $reflectionFactories = new \ReflectionProperty($container, 'factories');
        $reflectionFactories->setAccessible(true);

        $factories = $reflectionFactories->getValue($container);

        self::assertCount(1, $factories);

        $factory = array_shift($factories);

        $service = $factory($container);

        self::assertInstanceOf(\stdClass::class, $service);
    }

    public function testFactory(): void
    {
        $container = new Container();
        $container->factory('id', static function () {
            return new \stdClass();
        });

        $reflectionFactories = new \ReflectionProperty($container, 'factories');
        $reflectionFactories->setAccessible(true);

        $factories = $reflectionFactories->getValue($container);

        self::assertCount(1, $factories);

        $factory = array_shift($factories);

        $service = $factory($container);

        self::assertInstanceOf(\stdClass::class, $service);
    }

    public function testFactoryExtend(): void
    {
        $factories = [];
        $factories['id'] = static function () {
            $object = new \stdClass();
            $object->key1 = 'value1';

            return $object;
        };

        $container = new Container();

        $reflectionFactories = new \ReflectionProperty($container, 'factories');
        $reflectionFactories->setAccessible(true);
        $reflectionFactories->setValue($container, $factories);

        $container->factory('id', static function (ContainerInterface $container, callable $previous) {
            $object = $previous($container);
            $object->key2 = 'value2';

            return $object;
        });

        $container->factory('id', static function (ContainerInterface $container, callable $previous) {
            $object = $previous($container);
            $object->key3 = 'value3';

            return $object;
        });

        $factories = $reflectionFactories->getValue($container);

        self::assertCount(1, $factories);

        $factory = array_shift($factories);

        $service = $factory($container);

        self::assertInstanceOf(\stdClass::class, $service);
        self::assertSame('value1', $service->key1);
        self::assertSame('value2', $service->key2);
        self::assertSame('value3', $service->key3);
    }

    public function testReplace(): void
    {
        $factories = [];
        $factories['id'] = static function (): void {
            throw new \Exception('should not be called!');
        };

        $container = new Container();

        $reflectionFactories = new \ReflectionProperty($container, 'factories');
        $reflectionFactories->setAccessible(true);
        $reflectionFactories->setValue($container, $factories);

        $container->factory('id', static function () {
            $object = new \stdClass();
            $object->key1 = 'value1';

            return $object;
        });

        $container->factory('id', static function (ContainerInterface $container, callable $previous) {
            $object = $previous($container);
            $object->key2 = 'value2';

            return $object;
        });

        $factories = $reflectionFactories->getValue($container);

        self::assertCount(1, $factories);

        $factory = array_shift($factories);

        $service = $factory($container);

        self::assertInstanceOf(\stdClass::class, $service);
        self::assertSame('value1', $service->key1);
        self::assertSame('value2', $service->key2);
    }

    /**
     * @covers \Chubbyphp\Container\Container::factory
     */
    public function testFactoryReplaceAfterServiceInstanciated(): void
    {
        $container = new Container();
        $container->factory('id', static function () {
            return new \stdClass();
        });

        $service1 = $container->get('id');

        $container->factory('id', static function () {
            return new \stdClass();
        });

        self::assertNotSame($service1, $container->get('id'));
    }

    public function testGet(): void
    {
        $factories = [];
        $factories['id'] = static function () {
            return new \stdClass();
        };

        $container = new Container();

        $reflectionFactories = new \ReflectionProperty($container, 'factories');
        $reflectionFactories->setAccessible(true);
        $reflectionFactories->setValue($container, $factories);

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);

        self::assertSame($service, $container->get('id'));
    }

    public function testGetWithMissingId(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('There is no service with id "id"');
        $this->expectExceptionCode(3);

        $container = new Container();
        $container->get('id');
    }

    public function testGetWithException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Could not create service with id "id"');
        $this->expectExceptionCode(1);

        $factories = [];
        $factories['id'] = static function (ContainerInterface $container): void {
            $container->get('unknown');
        };

        $container = new Container();

        $reflectionFactories = new \ReflectionProperty($container, 'factories');
        $reflectionFactories->setAccessible(true);
        $reflectionFactories->setValue($container, $factories);

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);

        self::assertSame($service, $container->get('id'));
    }

    public function testHas(): void
    {
        $container = new Container();

        self::assertFalse($container->has('id'));

        $factories = [];
        $factories['id'] = static function () {
            return new \stdClass();
        };

        $reflectionFactories = new \ReflectionProperty($container, 'factories');
        $reflectionFactories->setAccessible(true);
        $reflectionFactories->setValue($container, $factories);

        self::assertTrue($container->has('id'));
    }
}
