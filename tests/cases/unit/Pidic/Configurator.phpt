<?php

/**
 * Test: Phalette\Pidic\Configurator
 */

use Phalette\Pidic\Configurator;
use Phalette\Pidic\Environment;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../../bootstrap.php';

// Default parameters
test(function () {
    $c = new Configurator();
    $c->setCacheDir(TEMP_DIR);
    $container = $c->createContainer();

    $cparams = $container->getParameters();
    Assert::equal([
        'appDir',
        'wwwDir',
        'debugMode',
        'productionMode',
        'environment',
        'consoleMode',
    ], array_keys($cparams));
});

// Simple parameters
test(function () {
    $params = ['foo' => 'bar'];
    $c = new Configurator($params);
    $c->setCacheDir(TEMP_DIR);
    $container = $c->createContainer();

    $cparams = $container->getParameters();
    Assert::equal($params['foo'], $cparams['foo']);
});

// Config parameters
test(function () {
    $c = new Configurator();
    $c->setCacheDir(TEMP_DIR);
    $c->addConfig(FileMock::create('
parameters:
    foo: bar
', 'neon'));
    $container = $c->createContainer();

    $cparams = $container->getParameters();
    Assert::equal('bar', $cparams['foo']);
});

// Modes
test(function () {
    $c = new Configurator();
    $c->setCacheDir(TEMP_DIR);
    $c->setMode(Environment::PRODUCTION);
    $container = $c->createContainer();
    $cparams = $container->getParameters();
    Assert::false($cparams['debugMode']);
    Assert::true($cparams['productionMode']);

    $c = new Configurator();
    $c->setCacheDir(TEMP_DIR);
    $c->setMode(Environment::DEVELOPMENT);
    $container = $c->createContainer();
    $cparams = $container->getParameters();
    Assert::true($cparams['debugMode']);
    Assert::false($cparams['productionMode']);
});