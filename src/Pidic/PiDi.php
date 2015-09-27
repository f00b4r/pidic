<?php

namespace Phalette\Pidic;

use Exception;
use Nette\DI\Container;
use Phalcon\Di;
use Phalcon\Di\Service as PhService;
use Phalcon\Di\ServiceInterface as PhServiceInterface;
use Phalcon\DiInterface as PhDiInterface;

class PiDi implements PhDiInterface
{

    /** @var PiDi */
    protected static $default;

    /** @var bool */
    protected $freshInstance;

    /** @var object[] */
    protected $sharedInstances = [];

    /** @var PiDiContainer|Container */
    private $container;

    /**
     * @param Container $container
     */
    function __construct(Container $container)
    {
        $this->container = $container;

        if (!self::$default) {
            self::$default = $this;
        }
    }

    /**
     * NETTE DI INTERFACE ******************************************************
     */

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $class
     * @param bool|TRUE $need
     * @return object
     */
    public function getByType($class, $need = TRUE)
    {
        return $this->container->getByType($class, $need);
    }

    /**
     * @param string $class
     * @return string[]
     */
    public function findByType($class)
    {
        return $this->container->findByType($class);
    }

    /**
     * @param string $tag
     * @return array
     */
    public function findByTag($tag)
    {
        return $this->container->findByTag($tag);
    }

    /**
     * PHALCON DI INTERFACE ****************************************************
     */

    /**
     * @param string $name
     * @param mixed $definition
     * @param bool $shared
     * @return object
     */
    public function set($name, $definition, $shared = FALSE)
    {
        if ($shared) {
            return $this->setShared($name, $definition);
        } else {
            return $this->container->addService($name, new PhService($name, $definition, FALSE));
        }
    }

    /**
     * @param string $name
     * @param mixed $definition
     * @return object
     */
    public function setShared($name, $definition)
    {
        return $this->container->addService($name, new PhService($name, $definition, TRUE));
    }

    /**
     * @param string $name
     * @return void
     */
    public function remove($name)
    {
        $this->container->removeService($name);
    }

    /**
     * @param string $name
     * @param string $definition
     * @param bool|FALSE $shared
     * @return bool
     */
    public function attempt($name, $definition, $shared = FALSE)
    {
        if (!$this->has($name)) {
            if ($shared) {
                return $this->setShared($name, $definition);
            } else {
                return $this->set($name, $definition);
            }
        }

        return FALSE;
    }

    /**
     * @param string $name
     * @param array $parameters
     * @return object
     */
    public function get($name, $parameters = NULL)
    {
        $def = $this->container->getService($name);

        if ($def instanceof PhServiceInterface) {
            return $def->resolve($parameters, $this);
        }

        return $def;
    }

    /**
     * @param string $name
     * @param array $parameters
     * @return object
     */
    public function getShared($name, $parameters = NULL)
    {
        if (!isset($this->sharedInstances[$name])) {
            $this->freshInstance = TRUE;
            $this->sharedInstances[$name] = $this->get($name, $parameters);
        } else {
            $this->freshInstance = FALSE;
        }

        return $this->sharedInstances[$name];
    }

    /**
     * @param string $name
     * @param PhServiceInterface $definition
     * @return object
     */
    public function setRaw($name, PhServiceInterface $definition)
    {
        return $this->container->addService($name, $definition);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getRaw($name)
    {
        $def = $this->container->getService($name);

        if ($def instanceof PhServiceInterface) {
            return $def->getDefinition();
        }

        return $def;
    }

    /**
     * @param string $name
     * @return object
     */
    public function getService($name)
    {
        return $this->container->getService($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->container->hasService($name);
    }

    /**
     * @return bool
     */
    public function wasFreshInstance()
    {
        return $this->freshInstance;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->container->getServices();
    }

    /**
     * @param PhDiInterface $di
     */
    public static function setDefault(PhDiInterface $di)
    {
        self::$default = $di;
    }

    /**
     * @return PhDiInterface
     */
    public static function getDefault()
    {
        return self::$default;
    }

    /**
     * @return void
     */
    public static function reset()
    {
        self::$default = NULL;
    }

    /**
     * MAGIC********************************************************************
     */

    /**
     * @param string $method
     * @param array $arguments
     * @return object
     * @throws Exception
     */
    public function __call($method, $arguments = [])
    {
        if (strrpos($method, 'get', -strlen($method)) !== FALSE) {
            return $this->get(lcfirst(substr($method, 3)), $arguments);
        } else if (strrpos($method, 'set', -strlen($method)) !== FALSE) {
            if (isset($arguments, $arguments[0])) {
                return $this->set(lcfirst(substr($method, 3)), $arguments[0]);
            }
        } else {
            throw new Exception("Call to undefined method or service '" . $method . "'");
        }
    }

    /**
     * ARRAY ACCESS ************************************************************
     */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param string $offset
     * @return object
     */
    public function offsetGet($offset)
    {
        return $this->getShared($offset);
    }

    /**
     * @param string $offset
     * @param object $value
     */
    public function offsetSet($offset, $value)
    {
        $this->setShared($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

}