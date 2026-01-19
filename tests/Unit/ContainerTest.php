<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Unit;

use Chubbyphp\Container\Container;
use Chubbyphp\Container\Exceptions\ContainerException;
use Chubbyphp\Container\Exceptions\ExistsException;
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
    /**
     * @covers \Chubbyphp\Container\Container::__construct
     */
    public function testConstruct(): void
    {
        $container = new Container([
            'id' => static fn (): \stdClass => new \stdClass(),
        ]);

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
    }

    /**
     * @covers \Chubbyphp\Container\Container::factories
     */
    public function testFactories(): void
    {
        $container = new Container();

        $container->factories([
            'id' => static fn (): \stdClass => new \stdClass(),
        ]);

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
    }

    /**
     * @covers \Chubbyphp\Container\Container::factory
     */
    public function testFactoryWithExistingPrototypeFactory(): void
    {
        $this->expectException(ExistsException::class);
        $this->expectExceptionMessage('Factory with id "id" already exists as "prototype factory"');

        $container = new Container();

        $container->prototypeFactory('id', static fn (): \stdClass => new \stdClass());

        $container->factory('id', static fn (): \stdClass => new \stdClass());
    }

    /**
     * @covers \Chubbyphp\Container\Container::factory
     */
    public function testFactory(): void
    {
        $container = new Container();

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
    }

    /**
     * @covers \Chubbyphp\Container\Container::factory
     */
    public function testFactoryExtend(): void
    {
        $container = new Container();

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
     * @covers \Chubbyphp\Container\Container::factory
     */
    public function testFactoryReplace(): void
    {
        $container = new Container();

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
     * @covers \Chubbyphp\Container\Container::factory
     */
    public function testFactoryReplaceAfterServiceInstantiated(): void
    {
        $container = new Container();

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        $service1 = $container->get('id');

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        self::assertNotSame($service1, $container->get('id'));
    }

    /**
     * @covers \Chubbyphp\Container\Container::prototypeFactories
     */
    public function testPrototypeFactories(): void
    {
        $container = new Container();

        $container->prototypeFactories([
            'id' => static fn (): \stdClass => new \stdClass(),
        ]);

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
    }

    /**
     * @covers \Chubbyphp\Container\Container::prototypeFactory
     */
    public function testPrototypeFactoryWithExistingFactory(): void
    {
        $this->expectException(ExistsException::class);
        $this->expectExceptionMessage('Factory with id "id" already exists as "factory"');

        $container = new Container();

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        $container->prototypeFactory('id', static fn (): \stdClass => new \stdClass());
    }

    /**
     * @covers \Chubbyphp\Container\Container::prototypeFactory
     */
    public function testPrototypeFactory(): void
    {
        $container = new Container();

        $container->prototypeFactory('id', static fn (): \stdClass => new \stdClass());

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);
    }

    /**
     * @covers \Chubbyphp\Container\Container::prototypeFactory
     */
    public function testPrototypeFactoryExtend(): void
    {
        $container = new Container();

        $container->prototypeFactory('id', static function (): \stdClass {
            $object = new \stdClass();
            $object->key1 = 'value1';

            return $object;
        });

        $container->prototypeFactory('id', static function (ContainerInterface $container, callable $previous): \stdClass {
            $object = $previous($container);
            $object->key2 = 'value2';

            return $object;
        });

        $container->prototypeFactory('id', static function (ContainerInterface $container, callable $previous): \stdClass {
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
     * @covers \Chubbyphp\Container\Container::prototypeFactory
     */
    public function testPrototypeFactoryReplace(): void
    {
        $container = new Container();

        $container->prototypeFactory('id', static function (): void {
            throw new \Exception('should not be called!');
        });

        $container->prototypeFactory('id', static function (): \stdClass {
            $object = new \stdClass();
            $object->key1 = 'value1';

            return $object;
        });

        $container->prototypeFactory('id', static function (ContainerInterface $container, callable $previous): \stdClass {
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
     * @covers \Chubbyphp\Container\Container::getFactory
     */
    public function testGetFactoryWithMissingId(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('There is no service with id "id"');
        $this->expectExceptionCode(3);

        $container = new Container();
        $container->getFactory('id');
    }

    /**
     * @covers \Chubbyphp\Container\Container::getFactory
     */
    public function testGetFactoryWithFactory(): void
    {
        $container = new Container();

        $factory = static fn (): \stdClass => new \stdClass();

        $container->factory('id', $factory);

        self::assertSame($factory, $container->getFactory('id'));
    }

    /**
     * @covers \Chubbyphp\Container\Container::getFactory
     */
    public function testGetFactoryWithPrototypeFactory(): void
    {
        $container = new Container();

        $factory = static fn (): \stdClass => new \stdClass();

        $container->prototypeFactory('id', $factory);

        self::assertSame($factory, $container->getFactory('id'));
    }

    /**
     * @covers \Chubbyphp\Container\Container::get
     */
    public function testGetWithMissingId(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('There is no service with id "id"');
        $this->expectExceptionCode(3);

        $container = new Container();
        $container->get('id');
    }

    /**
     * @covers \Chubbyphp\Container\Container::get
     */
    public function testGetWithFactory(): void
    {
        $container = new Container();

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);

        self::assertSame($service, $container->get('id'));
    }

    /**
     * @covers \Chubbyphp\Container\Container::get
     */
    public function testGetWithPrototypeFactory(): void
    {
        $container = new Container();

        $container->prototypeFactory('id', static fn (): \stdClass => new \stdClass());

        $service = $container->get('id');

        self::assertInstanceOf(\stdClass::class, $service);

        self::assertNotSame($service, $container->get('id'));
    }

    /**
     * @covers \Chubbyphp\Container\Container::get
     */
    public function testGetWithFactoryAndException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Could not create service with id "id"');
        $this->expectExceptionCode(1);

        $container = new Container();

        $container->factory('id', static function (ContainerInterface $container): void {
            $container->get('unknown');
        });

        $container->get('id');
    }

    /**
     * @covers \Chubbyphp\Container\Container::get
     */
    public function testGetWithPrototypeFactoryAndException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Could not create service with id "id"');
        $this->expectExceptionCode(1);

        $container = new Container();

        $container->prototypeFactory('id', static function (ContainerInterface $container): void {
            $container->get('unknown');
        });

        $container->get('id');
    }

    /**
     * @covers \Chubbyphp\Container\Container::has
     */
    public function testHasWithFactory(): void
    {
        $container = new Container();

        self::assertFalse($container->has('id'));

        $container->factory('id', static fn (): \stdClass => new \stdClass());

        self::assertTrue($container->has('id'));
    }

    /**
     * @covers \Chubbyphp\Container\Container::has
     */
    public function testHasWithPrototypeFactory(): void
    {
        $container = new Container();

        self::assertFalse($container->has('id'));

        $container->prototypeFactory('id', static fn (): \stdClass => new \stdClass());

        self::assertTrue($container->has('id'));
    }
}
