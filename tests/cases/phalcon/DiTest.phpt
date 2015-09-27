<?php

/**
 * Test: Phalcon\DiTest
 */

use Phalette\Pidic\Configurator;
use Phalette\Pidic\Extensions\PhalconDefaultsExtension;
use Phalette\Pidic\PiDi;
use Phalette\Pidic\PiDiContainer;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2015 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

class InjectableComponent
{
    public $response;

    public $other;

    public function __construct($response = NULL)
    {
        $this->response = $response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}

class SimpleComponent
{

}

class SomeComponent
{

    public $someProperty = FALSE;

    public function __construct($value)
    {
        $this->someProperty = $value;
    }

}

class Service1242
{
}

class DiTest extends BaseTestCase
{

    /** @var PiDi */
    protected $_di;

    public function setUp()
    {
        parent::setUp();
        Phalcon\Di::reset();
        $this->_di = new Pidi(new PiDiContainer());
    }

    public function testSetString()
    {

        $this->_di->set('request1', 'Phalcon\Http\Request');

        $request = $this->_di->get('request1');
        Assert::equal(get_class($request), 'Phalcon\Http\Request');
    }

    public function testSetAnonymousFunction()
    {

        $this->_di->set('request2', function () {
            return new Phalcon\Http\Request();
        });

        $request = $this->_di->get('request2');
        Assert::equal(get_class($request), 'Phalcon\Http\Request');
    }

    public function testSetArray()
    {

        $this->_di->set('request3', array(
            'className' => 'Phalcon\Http\Request'
        ));

        $request = $this->_di->get('request3');
        Assert::equal(get_class($request), 'Phalcon\Http\Request');
    }

    public function testAtempt()
    {

        $this->_di->set('request4', function () {
            return new Phalcon\Http\Request();
        });

        $this->_di->attempt('request4', function () {
            return new stdClass();
        });

        $this->_di->attempt('request5', function () {
            return new stdClass();
        });

        $request = $this->_di->get('request4');
        Assert::equal(get_class($request), 'Phalcon\Http\Request');

        $request = $this->_di->get('request5');
        Assert::equal(get_class($request), 'stdClass');

    }

    public function testHas()
    {

        $this->_di->set('request6', function () {
            return new Phalcon\Http\Request();
        });

        Assert::true($this->_di->has('request6'));
        Assert::false($this->_di->has('request7'));
    }

    public function testShared()
    {

        $this->_di->set('dateObject', function () {
            $object = new stdClass();
            $object->date = microtime(TRUE);
            return $object;
        });

        $dateObject = $this->_di->getShared('dateObject');
        Assert::type('stdClass', $dateObject);
        Assert::true($this->_di->wasFreshInstance());

        $dateObject2 = $this->_di->getShared('dateObject');
        Assert::type('stdClass', $dateObject);

        Assert::equal($dateObject->date, $dateObject2->date);

    }

    public function testMagicCall()
    {

        $this->_di->set('request8', 'Phalcon\Http\Request');

        $request = $this->_di->getRequest8();
        Assert::equal(get_class($request), 'Phalcon\Http\Request');

        $this->_di->setRequest9('Phalcon\Http\Request');
        $request = $this->_di->get('request9');
        Assert::equal(get_class($request), 'Phalcon\Http\Request');

    }

    public function testParameters()
    {
        $this->_di->set('someComponent1', function ($v) {
            return new SomeComponent($v);
        });

        $this->_di->set('someComponent2', 'SomeComponent');

        $someComponent1 = $this->_di->get('someComponent1', array(100));
        Assert::equal($someComponent1->someProperty, 100);

        $someComponent2 = $this->_di->get('someComponent2', array(50));
        Assert::equal($someComponent2->someProperty, 50);
    }

    public function skip_testGetServices()
    {
        $expectedServices = array(
            'service1' => Phalcon\Di\Service::__set_state(array(
                '_name' => 'service1',
                '_definition' => 'some-service',
                '_shared' => FALSE,
                '_sharedInstance' => NULL,
            )),
            'service2' => Phalcon\Di\Service::__set_state(array(
                '_name' => 'service2',
                '_definition' => 'some-other-service',
                '_shared' => FALSE,
                '_sharedInstance' => NULL,
            ))
        );

        $this->_di->set('service1', 'some-service');
        $this->_di->set('service2', 'some-other-service');
        Assert::equal($expectedServices, $this->_di->getServices());
    }

    public function testGetRawService()
    {
        $this->_di->set('service1', 'some-service');
        Assert::equal('some-service', $this->_di->getRaw('service1'));
    }

    public function testArrayAccess()
    {
        $this->_di['simple'] = 'SimpleComponent';
        Assert::equal(get_class($this->_di['simple']), 'SimpleComponent');
    }

    public function testComplexInjection()
    {

        $response = new Phalcon\Http\Response();
        $this->_di->set('response', $response);

        //Injection of parameters in the constructor
        $this->_di->set('simpleConstructor',
            array(
                'className' => 'InjectableComponent',
                'arguments' => array(
                    array('type' => 'parameter', 'value' => 'response')
                )
            )
        );

        //Injection of simple setters
        $this->_di->set('simpleSetters',
            array(
                'className' => 'InjectableComponent',
                'calls' => array(
                    array(
                        'method' => 'setResponse',
                        'arguments' => array(
                            array('type' => 'parameter', 'value' => 'response'),
                        )
                    ),
                )
            )
        );

        //Injection of properties
        $this->_di->set('simpleProperties',
            array(
                'className' => 'InjectableComponent',
                'properties' => array(
                    array(
                        'name' => 'response', 'value' => array('type' => 'parameter', 'value' => 'response')
                    ),
                )
            )
        );

        //Injection of parameters in the constructor resolving the service parameter
        $this->_di->set('complexConstructor',
            array(
                'className' => 'InjectableComponent',
                'arguments' => array(
                    array('type' => 'service', 'name' => 'response')
                )
            )
        );

        //Injection of simple setters resolving the service parameter
        $this->_di->set('complexSetters',
            array(
                'className' => 'InjectableComponent',
                'calls' => array(
                    array(
                        'method' => 'setResponse',
                        'arguments' => array(
                            array('type' => 'service', 'name' => 'response')
                        )
                    ),
                )
            )
        );

        //Injection of properties resolving the service parameter
        $this->_di->set('complexProperties',
            array(
                'className' => 'InjectableComponent',
                'properties' => array(
                    array(
                        'name' => 'response', 'value' => array('type' => 'service', 'name' => 'response')
                    ),
                )
            )
        );

        $component = $this->_di->get('simpleConstructor');
        Assert::type('InjectableComponent', $component);
        Assert::type('string', $component->getResponse());
        Assert::equal($component->getResponse(), 'response');

        $component = $this->_di->get('simpleSetters');
        Assert::true(is_string($component->getResponse()));
        Assert::equal($component->getResponse(), 'response');

        $component = $this->_di->get('simpleProperties');
        Assert::true(is_string($component->getResponse()));
        Assert::equal($component->getResponse(), 'response');

        $component = $this->_di->get('complexConstructor');
        Assert::true(is_object($component->getResponse()));
        Assert::equal($component->getResponse(), $response);

        $component = $this->_di->get('complexSetters');
        Assert::true(is_object($component->getResponse()));
        Assert::equal($component->getResponse(), $response);

        $component = $this->_di->get('complexProperties');
        Assert::true(is_object($component->getResponse()));
        Assert::equal($component->getResponse(), $response);
    }

    public function testFactoryDefault()
    {
        $configurator = new Configurator();
        $configurator->setCacheDir(TEMP_DIR);
        $configurator->onCompile[] = function ($compiler) {
            $compiler->addExtension('phalcon', new PhalconDefaultsExtension());
        };
        $factoryDefault = new PiDi($configurator->createContainer());

        $request = $factoryDefault->get('request');
        Assert::equal(get_class($request), 'Phalcon\Http\Request');

        $response = $factoryDefault->get('response');
        Assert::equal(get_class($response), 'Phalcon\Http\Response');

        $filter = $factoryDefault->get('filter');
        Assert::equal(get_class($filter), 'Phalcon\Filter');

        $escaper = $factoryDefault->get('escaper');
        Assert::equal(get_class($escaper), 'Phalcon\Escaper');

        $url = $factoryDefault->get('url');
        Assert::equal(get_class($url), 'Phalcon\Mvc\Url');

        $flash = $factoryDefault->get('flash');
        Assert::equal(get_class($flash), 'Phalcon\Flash\Direct');

        $dispatcher = $factoryDefault->get('dispatcher');
        Assert::equal(get_class($dispatcher), 'Phalcon\Mvc\Dispatcher');

        $modelsManager = $factoryDefault->get('modelsManager');
        Assert::equal(get_class($modelsManager), 'Phalcon\Mvc\Model\Manager');

        $modelsMetadata = $factoryDefault->get('modelsMetadata');
        Assert::equal(get_class($modelsMetadata), 'Phalcon\Mvc\Model\MetaData\Memory');

        $router = $factoryDefault->get('router');
        Assert::equal(get_class($router), 'Phalcon\Mvc\Router');

        $session = $factoryDefault->get('session');
        Assert::equal(get_class($session), 'Phalcon\Session\Adapter\Files');

        $security = $factoryDefault->get('security');
        Assert::equal(get_class($security), 'Phalcon\Security');

    }

    public function testStaticDi()
    {
        $di = PiDi::getDefault();
        Assert::type(PiDi::class, $di);
    }

    public function testCrash()
    {
        $configurator = new Configurator();
        $configurator->setCacheDir(TEMP_DIR);
        $configurator->onCompile[] = function ($compiler) {
            $compiler->addExtension('phalcon', new PhalconDefaultsExtension());
        };
        $di = new PiDi($configurator->createContainer());
        $bag = $di->get('sessionBag', array('dummy'));
        Assert::true(TRUE);
    }

    /**
     * Prior to 2.0.0 exception message was "Service 'servicewhichdoesnotexist' wasn't found in the dependency injection container"
     */
    public function testGettingNonExistentServiceShouldThrowExceptionWithExpectedMessage()
    {
        Assert::throws(function () {
            $di = new PiDi(new PiDiContainer());
            $di->get('servicewhichdoesnotexist');
        }, 'Nette\DI\MissingServiceException');
    }

    public function testIssue1242()
    {
        $di = new PiDi(new PiDiContainer());
        $di->set('resolved', function () {
            return new Service1242();
        });
        $di->set('notresolved', function () {
            return new Service1242();
        });

        Assert::false($di->getService('resolved')->isResolved());
        Assert::false($di->getService('notresolved')->isResolved());

        $di->get('resolved');

        Assert::true($di->getService('resolved')->isResolved());
        Assert::false($di->getService('notresolved')->isResolved());
    }
}

(new DiTest())->run();