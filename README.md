# PiDiC

[![Phalconist](https://phalconist.com/phalette/pidic/default.svg)](https://phalconist.com/phalette/pidic)
[![Build Status](https://img.shields.io/travis/phalette/pidic.svg?style=flat-square)](https://travis-ci.org/phalette/pidic)
[![Code coverage](https://img.shields.io/coveralls/phalette/pidic.svg?style=flat-square)](https://coveralls.io/r/phalette/pidic)
[![Downloads this Month](https://img.shields.io/packagist/dt/phalette/pidic.svg?style=flat-square)](https://packagist.org/packages/phalette/pidic)
[![Latest stable](https://img.shields.io/packagist/v/phalette/pidic.svg?style=flat-square)](https://packagist.org/packages/phalette/pidic)
[![HHVM Status](https://img.shields.io/hhvm/phalette/pidic.svg?style=flat-square)](http://hhvm.h4cc.de/package/phalette/pidic)

PiDiC is an adapter over [Nette\Di\Container](https://api.nette.org/2.3/Nette.DI.Container.html).

## Install

```sh
$ composer require phalette/pidic:dev-master
```

### Dependencies

* **PHP >= 5.5.0**
* [Nette\Di >= 2.3.0](https://github.com/nette/di)
* [Phalcon >= 2.0.0](https://github.com/phalcon/cphalcon/)

## Configuration

```php
use Nette\DI\Compiler;
use Phalette\Pidic\Configurator;
use Phalette\Pidic\Environment;
use Phalette\Pidic\Extensions\PhalconDefaultsExtension;
use Phalette\Pidic\Extensions\PhalconExtension;
use Phalette\Pidic\PiDi;

$configurator = new Configurator();
$configurator->setMode(Environment::DEVELOPMENT);
$configurator->setCacheDir(__DIR__ . '/cache');
$configurator->onCompile[] = function (Compiler $compiler) {
    $compiler->addExtension('phalcon', new PhalconExtension());
    $compiler->addExtension('phalconDefaults', new PhalconDefaultsExtension());
};

$container = $configurator->createContainer();
$pidi = $container->getService('pidi');
```

### Learn by working example

This is based on [official tutorial](https://docs.phalconphp.com/en/latest/reference/tutorial.html).
```php
use Nette\DI\Compiler;
use Phalette\Pidic\Configurator;
use Phalette\Pidic\Environment;
use Phalette\Pidic\Extensions\PhalconDefaultsExtension;
use Phalette\Pidic\Extensions\PhalconExtension;
use Phalette\Pidic\PiDi;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

try {

    // Register an autoloader
    $loader = new Loader();
    $loader->registerDirs(array(
        '../app/controllers/',
        '../app/models/'
    ))->register();

    // Create a DI
    $configurator = new Configurator();
    $configurator->setMode(Environment::DEVELOPMENT);
    $configurator->setCacheDir(__DIR__ . '/cache');
    $configurator->onCompile[] = function (Compiler $compiler) {
        $compiler->addExtension('phalcon', new PhalconExtension());
        $compiler->addExtension('phalconDefaults', new PhalconDefaultsExtension());
    };
    $container = $configurator->createContainer();
    $di = $container->getService('pidi');

    // Setup the view component
    $di->set('view', function () {
        $view = new View();
        $view->setViewsDir('../app/views/');
        return $view;
    });

    // Setup a base URI so that all generated URIs include the "tutorial" folder
    $di->set('url', function () {
        $url = new UrlProvider();
        $url->setBaseUri('/tutorial/');
        return $url;
    });

    // Handle the request
    $application = new Application($di);

    echo $application->handle()->getContent();

} catch (\Exception $e) {
     echo "PhalconException: ", $e->getMessage();
}
```

### PhalconExtension

It sets self-instance over static `Phalcon\Di::setDefault()`. Every object extending from `Phalcon\Di\InjectionAwareInterface` can access **PiDiC** from `$this->getDI()`.

### PhalconDefaultsExtension

This extension replace `Phalcon\DI\FactoryDefault`. It register to the container 22 base services ([more in docs](https://docs.phalconphp.com/en/latest/reference/di.html#service-name-conventions)).

## Phalcon\Di

**PiDiC** implements [Phalcon\DiInterface](https://docs.phalconphp.com/en/latest/api/Phalcon_DI.html) and then you can change DI without any changes.

How to work with DI in Phalcon, you can [read here](https://docs.phalconphp.com/en/latest/reference/di.html).

## Nette\DI

Please read articles at Nette documentation:

* [Configuration](https://doc.nette.org/en/2.3/configuring)
* [Dependency Injection](https://doc.nette.org/en/2.3/dependency-injection)
* [Di usage](https://doc.nette.org/en/2.3/di-usage)

But the main article is:

* [Extensions](https://doc.nette.org/en/2.3/di-extensions)