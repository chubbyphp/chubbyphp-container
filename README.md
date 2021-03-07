# chubbyphp-container

[![CI](https://github.com/chubbyphp/chubbyphp-container/workflows/CI/badge.svg?branch=master)](https://github.com/chubbyphp/chubbyphp-container/actions?query=workflow%3ACI)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-container/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-container?branch=master)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/chubbyphp/chubbyphp-container/master)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/chubbyphp-container/master)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-container/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-container)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-container/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-container)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-container/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-container)

[![bugs](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=bugs)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![code_smells](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=code_smells)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![coverage](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=coverage)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![duplicated_lines_density](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![ncloc](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=ncloc)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![sqale_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![alert_status](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=alert_status)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![reliability_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![security_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=security_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![sqale_index](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)
[![vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-container&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-container)


## Description

A minimal Dependency Injection Container (DIC) which implements PSR-11. [DI Container Benchmark][3].

There is a laminas service manager adapter at [chubbyphp/chubbyphp-laminas-config][4].

## Requirements

 * php: ^7.4|^8.0
 * [psr/container][2]: ^1.1|^2.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-container][1].

```sh
composer require chubbyphp/chubbyphp-container "^2.0"
```

## Usage

There are two PSR-11 implementations:

 * `Chubbyphp\Container\Container` prototype (each get will return a new instance) and shared services
 * `Chubbyphp\Container\MinimalContainer` shared services

### MinimalContainer / Container

#### Factories

```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\MinimalContainer;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

$container = new MinimalContainer();
$container->factories([
    MyService::class => static function (ContainerInterface $container): MyService {
        return new MyService($container->get(LoggerInterface::class));
    },
]);
```

#### Factory

```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\MinimalContainer;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

$container = new MinimalContainer();

// new
$container->factory(MyService::class, static function (ContainerInterface $container): MyService {
    return new MyService($container->get(LoggerInterface::class));
});

// existing (replace)
$container->factory(MyService::class, static function (ContainerInterface $container): MyService {
    return new MyService($container->get(LoggerInterface::class));
});

// existing (extend)
$container->factory(
    MyService::class,
    static function (ContainerInterface $container, callable $previous): MyService {
        $myService = $previous($container);
        $myService->setLogger($container->get(LoggerInterface::class));

        return $myService;
    }
);
```

#### Factory with Parameter

```php
<?php

use Chubbyphp\Container\MinimalContainer;
use Chubbyphp\Container\Parameter;

$container = new MinimalContainer();
$container->factory('key', new Parameter('value'));
```

#### Get

```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\MinimalContainer;

$container = new MinimalContainer();

$myService = $container->get(MyService::class);
```

#### Has

```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\MinimalContainer;

$container = new MinimalContainer();
$container->has(MyService::class);
```

### Container

All methods of the `MinimalContainer` and the following:

#### Prototype Factories

**each get will return a new instance**

```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

$container = new Container();
$container->prototypeFactories([
    MyService::class => static function (ContainerInterface $container): MyService {
        return new MyService($container->get(LoggerInterface::class));
    },
]);
```

#### Prototype Factory

**each get will return a new instance**

```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

$container = new Container();

// new
$container->prototypeFactory(
    MyService::class,
    static function (ContainerInterface $container): MyService {
        return new MyService($container->get(LoggerInterface::class));
    }
);

// existing (replace)
$container->prototypeFactory(
    MyService::class,
    static function (ContainerInterface $container): MyService {
        return new MyService($container->get(LoggerInterface::class));
    }
);

// existing (extend)
$container->prototypeFactory(
    MyService::class,
    static function (ContainerInterface $container, callable $previous): MyService {
        $myService = $previous($container);
        $myService->setLogger($container->get(LoggerInterface::class));

        return $myService;
    }
);
```

## Migration

* [From Pimple][5]

## Copyright

Dominik Zogg 2021

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-container
[2]: https://packagist.org/packages/psr/container
[3]: https://rawgit.com/kocsismate/php-di-container-benchmarks/master/var/benchmark.html
[4]: https://github.com/chubbyphp/chubbyphp-laminas-config
[5]: doc/MigrateFromPimple.md
