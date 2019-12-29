# chubbyphp-container

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-container.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-container)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-container/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-container?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-container/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-container)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-container/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-container)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-container/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-container)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-container/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-container)

## Description

A simple PSR-11 container implementation.

## Requirements

 * php: ^7.2
 * [psr/container][2]: ^1.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-container][1].

```sh
composer require chubbyphp/chubbyphp-container "^1.0"
```

## Usage

### Factories

```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

$container = new Container();
$container->factories([
    MyService::class => static function (ContainerInterface $container) {
        return new MyService($container->get(LoggerInterface::class));
    },
]);
```

### Factory


```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

$container = new Container();

// new
$container->factory(MyService::class, static function (ContainerInterface $container) {
    return new MyService($container->get(LoggerInterface::class));
});

// existing (replace)
$container->factory(MyService::class, static function (ContainerInterface $container) {
    return new MyService($container->get(LoggerInterface::class));
});

// existing (extend)
$container->factory(
    MyService::class,
    static function (ContainerInterface $container, callable $previous) {
        $myService = $previous($container);
        $myService->setLogger($container->get(LoggerInterface::class));

        return $myService;
    }
);
```

### Get

```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\Container;

$container = new Container();

$myService = $container->get(MyService::class);
```

### Has

```php
<?php

use App\Service\MyService;
use Chubbyphp\Container\Container;

$container = new Container();
$container->has(MyService::class);
```

## Copyright

Dominik Zogg 2019

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-container

[2]: https://packagist.org/packages/psr/container
