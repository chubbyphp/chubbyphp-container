# Migrate from Pimple

## Defining Services

### Pimple

```php
// define some services
$container['session_storage'] = static function (Container $container): SessionStorage {
    return new SessionStorage('SESSION_ID');
};

$container['session'] = static function (Container $container): Session {
    return new Session($container['session_storage']);
};

// get the session object
$session = $container['session'];

// the above call is roughly equivalent to the following code:
// $storage = new SessionStorage('SESSION_ID');
// $session = new Session($storage);
```

### Chubbyphp

```php
// define some services
$container->factory('session_storage', static function (): SessionStorage {
    return new SessionStorage('SESSION_ID');
});

$container->factory('session', static function (ContainerInterface $container): Session {
    return new Session($container->get('session_storage'));
});

// get the session object
$session = $container->get('session');

// the above call is roughly equivalent to the following code:
// $storage = new SessionStorage('SESSION_ID');
// $session = new Session($storage);
```

## Defining Factory Services

### Pimple

```php
$container['session'] = $container->factory(static function (Container $container): Session {
    return new Session($container['session_storage']);
});
```

### Chubbyphp

```php
$container->prototypeFactory('session', static function (ContainerInterface $container): Session {
    return new Session($container->get('session_storage'));
});
```

## Defining Parameters

### Pimple

```php
// define some parameters
$container['cookie_name'] = 'SESSION_ID';
$container['session_storage_class'] = 'SessionStorage';

$container['session_storage'] = static function (Container $container): SessionStorage {
    return new $container['session_storage_class']($container['cookie_name']);
};
```

### Chubbyphp

```php
// define some parameters
$container->factory('cookie_name', new Parameter('SESSION_ID'));
$container->factory('session_storage_class', new Parameter('SessionStorage'));

$container->factory('session_storage', static function (ContainerInterface $container): SessionStorage {
    return new ($container->get('session_storage_class'))($container->get('cookie_name'));
});
```

## Protecting Parameters

### Pimple

```php
$container['random_func'] = $container->protect(static function (): int {
    return rand();
});
```

### Chubbyphp

```php
$container->factory('random_func', new Parameter(static function (): int {
    return rand();
}));
```

## Modifying Services after Definition

### Pimple

```php
$container['session_storage'] = static function (Container $container): SessionStorage {
    return new $container['session_storage_class']($container['cookie_name']);
};

$container->extend('session_storage', static function ($storage, $c): SessionStorage {
    $storage->...();

    return $storage;
});
```

### Chubbyphp

```php
$container->factory('session_storage', static function (ContainerInterface $container): SessionStorage {
    $sessionStorageClass = $container->get('session_storage_class');

    return new $sessionStorageClass($container->get('cookie_name'));
});

$container->factory('session_storage', static function (ContainerInterface $c, callable $previous): SessionStorage {
    $storage = $previous($c);

    $storage->...();

    return $storage;
});
```

## Extending a Container

### Pimple

```php
class FooProvider implements Pimple\ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $container['session_storage'] = static function (Container $container): SessionStorage {
            return new $container['session_storage_class']($container['cookie_name']);
        };
    }
}

$pimple->register(new FooProvider());
```

### Chubbyphp

```php
class FooProvider
{
    public function __invoke(): array
    {
        return [
            'session_storage' => static function (ContainerInterface $container): SessionStorage {
                $sessionStorageClass = $container->get('session_storage_class');

                return new $sessionStorageClass($container->get('cookie_name'));
            },
        ];
    }
}

$container->factories((new FooProvider())());
```

## Fetching the Service Creation Function

### Pimple

```php
$container['session'] = static function (Container $container): Session {
    return new Session($container['session_storage']);
};

$sessionFunction = $container->raw('session');
```

### Chubbyphp

```php
$container->factory('session', static function (ContainerInterface $container): Session {
    return new Session($container->get('session_storage'));
});

$sessionFunction = $container->getFactory('session');
```
