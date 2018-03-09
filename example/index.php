<?php

use Nette\DI\Compiler;
use Phalette\Pidic\Configurator;
use Phalette\Pidic\Environment;
use Phalette\Pidic\Extensions\PhalconDefaultsExtension;
use Phalette\Pidic\Extensions\PhalconExtension;
use Phalette\Pidic\PiDi;
use Tracy\Debugger;

require_once __DIR__ . '/../vendor/autoload.php';

Debugger::enable(Debugger::DETECT, __DIR__);
Debugger::$strictMode = TRUE;
Debugger::$maxDepth = 20;

$configurator = new Configurator();
$configurator->setMode(Environment::DEVELOPMENT);
$configurator->setCacheDir(__DIR__ . '/cache');
$configurator->onCompile[] = function (Compiler $compiler) {
    $compiler->addExtension('phalcon', new PhalconExtension());
    $compiler->addExtension('phalconDefaults', new PhalconDefaultsExtension());
};

$container = $configurator->createContainer();
//Debugger::dump($container);

/** @var PiDi $pidi */
$pidi = $container->getService('pidi');
Debugger::dump($pidi->getService('router'));
Debugger::dump($pidi->getShared('router'));


