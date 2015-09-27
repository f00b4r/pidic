<?php

namespace Phalette\Pidic\Extensions;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\PhpLiteral;

class PhalconDefaultsExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('ph_router'))
            ->setClass('Phalcon\Di\Service', ['router', 'Phalcon\Mvc\Router', TRUE]);
        $builder->addDefinition($this->prefix('router'))
            ->setClass('Phalcon\Mvc\Router');

        $builder->addDefinition($this->prefix('ph_dispatcher'))
            ->setClass('Phalcon\Di\Service', ['dispatcher', 'Phalcon\Mvc\Dispatcher', TRUE]);
        $builder->addDefinition($this->prefix('dispatcher'))
            ->setClass('Phalcon\Mvc\Dispatcher');

        $builder->addDefinition($this->prefix('ph_url'))
            ->setClass('Phalcon\Di\Service', ['url', 'Phalcon\Mvc\Url', TRUE]);
        $builder->addDefinition($this->prefix('url'))
            ->setClass('Phalcon\Mvc\Url');

        $builder->addDefinition($this->prefix('ph_modelsManager'))
            ->setClass('Phalcon\Di\Service', ['modelsManager', 'Phalcon\Mvc\Model\Manager', TRUE]);
        $builder->addDefinition($this->prefix('modelsManager'))
            ->setClass('Phalcon\Mvc\Model\Manager');

        $builder->addDefinition($this->prefix('ph_modelsMetadata'))
            ->setClass('Phalcon\Di\Service', ['modelsMetadata', 'Phalcon\Mvc\Model\MetaData\Memory', TRUE]);
        $builder->addDefinition($this->prefix('modelsMetadata'))
            ->setClass('Phalcon\Mvc\Model\MetaData\Memory');

        $builder->addDefinition($this->prefix('ph_response'))
            ->setClass('Phalcon\Di\Service', ['response', 'Phalcon\Http\Response', TRUE]);
        $builder->addDefinition($this->prefix('response'))
            ->setClass('Phalcon\Http\Response');

        $builder->addDefinition($this->prefix('ph_cookies'))
            ->setClass('Phalcon\Di\Service', ['cookies', 'Phalcon\Http\Response\Cookies', TRUE]);
        $builder->addDefinition($this->prefix('cookies'))
            ->setClass('Phalcon\Http\Response\Cookies');

        $builder->addDefinition($this->prefix('ph_request'))
            ->setClass('Phalcon\Di\Service', ['request', 'Phalcon\Http\Request', TRUE]);
        $builder->addDefinition($this->prefix('request'))
            ->setClass('Phalcon\Http\Request');

        $builder->addDefinition($this->prefix('ph_filter'))
            ->setClass('Phalcon\Di\Service', ['filter', 'Phalcon\Filter', TRUE]);
        $builder->addDefinition($this->prefix('filter'))
            ->setClass('Phalcon\Filter');

        $builder->addDefinition($this->prefix('ph_escaper'))
            ->setClass('Phalcon\Di\Service', ['escaper', 'Phalcon\Escaper', TRUE]);
        $builder->addDefinition($this->prefix('escaper'))
            ->setClass('Phalcon\Escaper');

        $builder->addDefinition($this->prefix('ph_security'))
            ->setClass('Phalcon\Di\Service', ['security', 'Phalcon\Security', TRUE]);
        $builder->addDefinition($this->prefix('security'))
            ->setClass('Phalcon\Crypt');

        $builder->addDefinition($this->prefix('ph_crypt'))
            ->setClass('Phalcon\Di\Service', ['crypt', 'Phalcon\Crypt', TRUE]);
        $builder->addDefinition($this->prefix('crypt'))
            ->setClass('Phalcon\Crypt');

        $builder->addDefinition($this->prefix('ph_annotations'))
            ->setClass('Phalcon\Di\Service', ['annotations', 'Phalcon\Annotations\Adapter\Memory', TRUE]);
        $builder->addDefinition($this->prefix('annotations'))
            ->setClass('Phalcon\Annotations\Adapter\Memory');

        $builder->addDefinition($this->prefix('ph_flash'))
            ->setClass('Phalcon\Di\Service', ['flash', 'Phalcon\Flash\Direct', TRUE]);
        $builder->addDefinition($this->prefix('flash'))
            ->setClass('Phalcon\Flash\Direct');

        $builder->addDefinition($this->prefix('ph_flashSession'))
            ->setClass('Phalcon\Di\Service', ['flashSession', 'Phalcon\Flash\Session', TRUE]);
        $builder->addDefinition($this->prefix('flashSession'))
            ->setClass('Phalcon\Flash\Session');

        $builder->addDefinition($this->prefix('ph_tag'))
            ->setClass('Phalcon\Di\Service', ['tag', 'Phalcon\Tag', TRUE]);
        $builder->addDefinition($this->prefix('tag'))
            ->setClass('Phalcon\Tag');

        $builder->addDefinition($this->prefix('ph_session'))
            ->setClass('Phalcon\Di\Service', ['session', 'Phalcon\Session\Adapter\Files', TRUE]);
        $builder->addDefinition($this->prefix('session'))
            ->setClass('Phalcon\Session\Adapter\Files');

        $builder->addDefinition($this->prefix('ph_sessionBag'))
            ->setClass('Phalcon\Di\Service', ['sessionBag', 'Phalcon\Session\Bag', TRUE]);
        $builder->addDefinition($this->prefix('sessionBag'))
            ->setFactory('Phalcon\Session\Bag')
            ->setParameters(['name'])
            ->setArguments([new PhpLiteral('$name')]);

        $builder->addDefinition($this->prefix('ph_eventsManager'))
            ->setClass('Phalcon\Di\Service', ['eventsManager', 'Phalcon\Events\Manager', TRUE]);
        $builder->addDefinition($this->prefix('eventsManager'))
            ->setClass('Phalcon\Events\Manager');

        $builder->addDefinition($this->prefix('ph_transactionManager'))
            ->setClass('Phalcon\Di\Service', ['transactionManager', 'Phalcon\Mvc\Model\Transaction\Manager', TRUE]);
        $builder->addDefinition($this->prefix('transactionManager'))
            ->setClass('Phalcon\Mvc\Model\Transaction\Manager');

        $builder->addDefinition($this->prefix('ph_assets'))
            ->setClass('Phalcon\Di\Service', ['assets', 'Phalcon\Assets\Manager', TRUE]);
        $builder->addDefinition($this->prefix('assets'))
            ->setClass('Phalcon\Assets\Manager');

        $builder->addAlias('router', $this->prefix('ph_router'));
        $builder->addAlias('dispatcher', $this->prefix('ph_dispatcher'));
        $builder->addAlias('url', $this->prefix('ph_url'));
        $builder->addAlias('modelsManager', $this->prefix('ph_modelsManager'));
        $builder->addAlias('modelsMetadata', $this->prefix('ph_modelsMetadata'));
        $builder->addAlias('response', $this->prefix('ph_response'));
        $builder->addAlias('cookies', $this->prefix('ph_cookies'));
        $builder->addAlias('request', $this->prefix('ph_request'));
        $builder->addAlias('filter', $this->prefix('ph_filter'));
        $builder->addAlias('escaper', $this->prefix('ph_escaper'));
        $builder->addAlias('security', $this->prefix('ph_security'));
        $builder->addAlias('crypt', $this->prefix('ph_crypt'));
        $builder->addAlias('annotations', $this->prefix('ph_annotations'));
        $builder->addAlias('flash', $this->prefix('ph_flash'));
        $builder->addAlias('flashSession', $this->prefix('ph_flashSession'));
        $builder->addAlias('tag', $this->prefix('ph_tag'));
        $builder->addAlias('session', $this->prefix('ph_session'));
        $builder->addAlias('sessionBag', $this->prefix('ph_sessionBag'));
        $builder->addAlias('eventsManager', $this->prefix('ph_eventsManager'));
        $builder->addAlias('transactionManager', $this->prefix('ph_transactionManager'));
        $builder->addAlias('assets', $this->prefix('ph_assets'));
    }

}
