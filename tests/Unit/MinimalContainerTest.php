<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Unit;

use Chubbyphp\Container\Exceptions\ContainerException;
use Chubbyphp\Container\Exceptions\NotFoundException;
use Chubbyphp\Container\MinimalContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Container\MinimalContainer
 *
 * @internal
 */
final class MinimalContainerTest extends TestCase
{
    /**
     * @covers \Chubbyphp\Container\MinimalContainer::__construct
     */
    public function testConstruct(): void
    {
        $container = new MinimalContainer([
            'id' => static fn (): \stdClass => new \stdClass(),
        ]);

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
    }

    /**
     * @covers \Chubbyphp\Container\MinimalContainer::factories
     */
    public function testFactories(): void
    {
        $container = new MinimalContainer();

        $container->factories([
            'id' => static fn (): \stdClass => new \stdClass(),
        ]);

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
    }

    /**
     * @covers \Chubbyphp\Container\MinimalContainer::factory
     */
    public function testFactory(): void
    {
        $container = new MinimalContainer();

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
    }

    /**
     * @covers \Chubbyphp\Container\MinimalContainer::factory
     */
    public function testFactoryExtend(): void
    {
        $container = new MinimalContainer();

        $container->factory('id', static function (): \stdClass {
            $object = new \stdClass();
            $object->key1 = 'value1';

            return $object;
        });

        $container->factory('id', static function (ContainerInterface $container, callable $previous): \stdClass {
            $object = $previous($container);
            $object->key2 = 'value2';

            return $object;
        });

        $container->factory('id', static function (ContainerInterface $container, callable $previous): \stdClass {
            $object = $previous($container);
            $object->key3 = 'value3';

            return $object;
        });

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
        self::assertSame('value1', $service->key1);
        self::assertSame('value2', $service->key2);
        self::assertSame('value3', $service->key3);
    }

    /**
     * @covers \Chubbyphp\Container\MinimalContainer::factory
     */
    public function testFactoryReplace(): void
    {
        $container = new MinimalContainer();

        $container->factory('id', static function (): void {
            throw new \Exception('should not be called!');
        });

        $container->factory('id', static function (): \stdClass {
            $object = new \stdClass();
            $object->key1 = 'value1';

            return $object;
        });

        $container->factory('id', static function (ContainerInterface $container, callable $previous): \stdClass {
            $object = $previous($container);
            $object->key2 = 'value2';

            return $object;
        });

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
        self::assertSame('value1', $service->key1);
        self::assertSame('value2', $service->key2);
    }

    /**
     * @covers \Chubbyphp\Container\MinimalContainer::factory
     */
    public function testFactoryReplaceAfterServiceInstanciated(): void
    {
        $container = new MinimalContainer();

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        $service1 = $container->get('id');

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        self::assertNotSame($service1, $container->get('id'));
    }

    /**
     * @covers \Chubbyphp\Container\MinimalContainer::get
     */
    public function testGetWithMissingId(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('There is no service with id "id"');
        $this->expectExceptionCode(3);

        $container = new MinimalContainer();
        $container->get('id');
    }

    /**
     * @covers \Chubbyphp\Container\MinimalContainer::get
     */
    public function testGetWithFactory(): void
    {
        $container = new MinimalContainer();

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);

        self::assertSame($service, $container->get('id'));
    }

    /**
     * @covers \Chubbyphp\Container\MinimalContainer::get
     */
    public function testGetWithFactoryAndException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Could not create service with id "id"');
        $this->expectExceptionCode(1);

        $container = new MinimalContainer();

        $container->factory('id', static function (ContainerInterface $container): void {
            $container->get('unknown');
        });

        $container->get('id');
    }

    /**
     * @covers \Chubbyphp\Container\MinimalContainer::has
     */
    public function testHasWithFactory(): void
    {
        $container = new MinimalContainer();

        self::assertFalse($container->has('id'));

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        self::assertTrue($container->has('id'));
    }
}
