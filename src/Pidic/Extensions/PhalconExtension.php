<?php

namespace Phalette\Pidic\Extensions;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Phalcon\Di;

class PhalconExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition('pidi')
            ->setClass('Phalette\Pidic\PiDi');
    }

    /**
     * @param ClassType $class
     */
    public function afterCompile(ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBody('Phalcon\Di::setDefault($this->getService(?));', ['pidi']);
    }

}
