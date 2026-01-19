<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Container\Integration;

use Chubbyphp\Container\Container as ChubbyphpContainer;
use Chubbyphp\Container\Parameter;
use Chubbyphp\Tests\Container\Helper\ChubbyphpFooProvider;
use Chubbyphp\Tests\Container\Helper\PimpleFooProvider;
use Chubbyphp\Tests\Container\Helper\Session;
use Chubbyphp\Tests\Container\Helper\SessionStorage;
use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;

/**
 * @internal
 *
 * @coversNothing
 */
final class MigrateFromPimpleTest extends TestCase
{
    /**
     * Tests all Pimple container features:
     * - Defining services (singleton by default)
     * - Defining factory services (non-singleton via factory())
     * - Defining parameters
     * - Protecting parameters (storing callables as values)
     * - Modifying services after definition (extend)
     * - Extending container with providers
     * - Fetching raw service creation function.
     */
    public function testPimple(): void
    {
        $container = new PimpleContainer();

        // Define parameters
        $container['cookie_name'] = 'SESSION_ID';
        $container['session_storage_class'] = SessionStorage::class;

        // Protect a callable (store it as a value, not as a service factory)
        $container['random_func'] = $container->protect(static fn (): int => random_int(0, getrandmax()));

        // Extend container with a provider (defines session_storage)
        $container->register(new PimpleFooProvider());

        // Modify service after definition (extend)
        $container->extend('session_storage', static function (SessionStorage $storage, PimpleContainer $c): SessionStorage {
            $storage->setData(['key' => 'value']);

            return $storage;
        });

        // Define another singleton service depending on session_storage
        $container['session'] = static fn (PimpleContainer $c): Session => new Session($c['session_storage']);

        // Define a factory service (non-singleton, new instance each time)
        $container['session_prototype'] = $container->factory(static fn (PimpleContainer $c): Session => new Session($c['session_storage']));

        // Assertions

        // Parameters work
        self::assertSame('SESSION_ID', $container['cookie_name']);
        self::assertSame(SessionStorage::class, $container['session_storage_class']);

        // Protected callable is returned as-is (not invoked)
        /** @var callable(): int $randomFunc */
        $randomFunc = $container['random_func'];
        self::assertIsCallable($randomFunc);
        self::assertIsInt($randomFunc());

        // Singleton service returns same instance
        /** @var SessionStorage $sessionStorage1 */
        $sessionStorage1 = $container['session_storage'];

        /** @var SessionStorage $sessionStorage2 */
        $sessionStorage2 = $container['session_storage'];
        self::assertInstanceOf(SessionStorage::class, $sessionStorage1);
        self::assertSame($sessionStorage1, $sessionStorage2);
        self::assertSame('SESSION_ID', $sessionStorage1->sessionId);

        // Extended service has modifications applied
        self::assertSame(['key' => 'value'], $sessionStorage1->getData());

        // Session service works with dependency injection
        /** @var Session $session */
        $session = $container['session'];
        self::assertInstanceOf(Session::class, $session);
        self::assertSame($sessionStorage1, $session->session);

        // Factory service returns different instances each time
        self::assertNotSame($container['session_prototype'], $container['session_prototype']);

        // Raw function can be fetched and invoked manually
        /** @var callable(PimpleContainer): Session $sessionFunction */
        $sessionFunction = $container->raw('session');
        self::assertIsCallable($sessionFunction);
        self::assertInstanceOf(Session::class, $sessionFunction($container));
    }

    /**
     * Tests all Chubbyphp container features:
     * - Defining services (singleton via factory())
     * - Defining factory services (non-singleton via prototypeFactory())
     * - Defining parameters (via Parameter class)
     * - Protecting parameters (storing callables via Parameter class)
     * - Modifying services after definition (via factory() with previous callable)
     * - Extending container with providers
     * - Fetching raw service creation function.
     */
    public function testChubbyphp(): void
    {
        $container = new ChubbyphpContainer();

        // Define parameters using Parameter class
        $container->factory('cookie_name', new Parameter('SESSION_ID'));
        $container->factory('session_storage_class', new Parameter(SessionStorage::class));

        // Protect a callable (store it as a value, not as a service factory)
        $container->factory('random_func', new Parameter(static fn (): int => random_int(0, getrandmax())));

        // Extend container with a provider (defines session_storage)
        $container->factories((new ChubbyphpFooProvider())());

        // Modify service after definition (via factory with previous callable)
        $container->factory('session_storage', static function (ChubbyphpContainer $c, callable $previous): SessionStorage {
            $storage = $previous($c);
            $storage->setData(['key' => 'value']);

            return $storage;
        });

        // Define another singleton service depending on session_storage
        $container->factory('session', static fn (ChubbyphpContainer $c): Session => new Session($c->get('session_storage')));

        // Define a prototype factory service (non-singleton, new instance each time)
        $container->prototypeFactory('session_prototype', static fn (ChubbyphpContainer $c): Session => new Session($c->get('session_storage')));

        // Assertions

        // Parameters work
        self::assertSame('SESSION_ID', $container->get('cookie_name'));
        self::assertSame(SessionStorage::class, $container->get('session_storage_class'));

        // Protected callable is returned as-is (not invoked)
        /** @var callable(): int $randomFunc */
        $randomFunc = $container->get('random_func');
        self::assertIsCallable($randomFunc);
        self::assertIsInt($randomFunc());

        // Singleton service returns same instance
        /** @var SessionStorage $sessionStorage1 */
        $sessionStorage1 = $container->get('session_storage');

        /** @var SessionStorage $sessionStorage2 */
        $sessionStorage2 = $container->get('session_storage');
        self::assertInstanceOf(SessionStorage::class, $sessionStorage1);
        self::assertSame($sessionStorage1, $sessionStorage2);
        self::assertSame('SESSION_ID', $sessionStorage1->sessionId);

        // Extended service has modifications applied
        self::assertSame(['key' => 'value'], $sessionStorage1->getData());

        // Session service works with dependency injection
        /** @var Session $session */
        $session = $container->get('session');
        self::assertInstanceOf(Session::class, $session);
        self::assertSame($sessionStorage1, $session->session);

        // Prototype factory service returns different instances each time
        self::assertNotSame($container->get('session_prototype'), $container->get('session_prototype'));

        // Raw factory function can be fetched and invoked manually
        /** @var callable(ChubbyphpContainer): Session $sessionFunction */
        $sessionFunction = $container->getFactory('session');
        self::assertIsCallable($sessionFunction);
        self::assertInstanceOf(Session::class, $sessionFunction($container));
    }
}
