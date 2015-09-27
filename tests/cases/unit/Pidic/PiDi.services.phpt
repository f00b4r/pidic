<?php

/**
 * Test: Phalette\Pidic\PiDi
 */

use Nette\DI\Container;
use Phalcon\Di\Service as PhService;
use Phalette\Pidic\Configurator;
use Phalette\Pidic\Extensions\PhalconDefaultsExtension;
use Phalette\Pidic\PiDi;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../../bootstrap.php';

class Foo
{
    public $x;

    function __construct($x)
    {
        $this->x = $x;
    }
}

class Foo2
{
    public $request;

    function __construct(Phalcon\Http\Request $request)
    {
        $this->request = $request;
    }
}

// Set/get service
test(function () {
    $container = new Container();
    $pidi = new PiDi($container);

    $foo1 = new stdClass();
    $pidi->set('foo1', $foo1);
    Assert::true($pidi->has('foo1'));
    Assert::same($foo1, $pidi->get('foo1'));

    $pidi->set('foo2', 'Phalcon\Http\Request');
    Assert::true($pidi->has('foo2'));
    Assert::type('Phalcon\Http\Request', $pidi->get('foo2'));
});

// Fresh instance
test(function () {
    $container = new Container();
    $pidi = new PiDi($container);

    $pidi->set('foo', new stdClass());
    Assert::true($pidi->has('foo'));

    $foo1 = $pidi->getShared('foo');
    Assert::true($pidi->wasFreshInstance());

    $foo2 = $pidi->getShared('foo');
    Assert::false($pidi->wasFreshInstance());

    Assert::same($foo1, $foo2);
});

// Phalcon Injection
test(function () {
    $container = new Container();
    $pidi = new PiDi($container);

    $pidi->set('foo', [
        'className' => 'Foo',
        'arguments' => [
            ['type' => 'parameter', 'value' => 'bar'],
        ]
    ]);
    $def = $pidi->get('foo');
    Assert::type('Foo', $def);
    Assert::type('string', $def->x);
    Assert::equal('bar', $def->x);
});

// Nette Injection
test(function () {
    $configurator = new Configurator();
    $configurator->setCacheDir(TEMP_DIR);
    $configurator->addConfig(FileMock::create('
services:
    foo: Foo2
', 'neon'));
    $configurator->onCompile[] = function ($compiler) {
        $compiler->addExtension('phalcon', new PhalconDefaultsExtension);
    };
    $container = $configurator->createContainer();
    $pidi = new PiDi($container);

    $def = $pidi->get('foo');
    Assert::type('Foo2', $def);
    Assert::type('Phalcon\Http\Request', $def->request);
});
