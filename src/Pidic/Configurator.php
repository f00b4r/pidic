<?php

namespace Phalette\Pidic;

use Nette\DI\Compiler;
use Nette\DI\Config\Loader;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;

class Configurator
{

    /** @var array */
    public $onCompile = [];

    /** @var */
    protected $cacheDir;

    /** @var array */
    protected $parameters = [];

    /** @var array */
    protected $configs = [];

    /** @var string */
    protected $mode;

    /** @var array */
    protected $excludedClasses = [
        'stdClass',
        'Phalcon\Di\Service',
        'Phalcon\Di\ServiceInterface'
    ];

    /**
     * @param array $parameters
     */
    function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * GETTERS/SETTERS *********************************************************
     */

    /**
     * @param string $config
     */
    public function addConfig($config)
    {
        $this->configs[] = $config;
    }

    /**
     * @param string $class
     */
    public function addExcludedClass($class)
    {
        $this->excludedClasses[] = $class;
    }

    /**
     * @param mixed $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * COMPILER ****************************************************************
     */

    /**
     * @return array
     */
    protected function getDefaultParameters()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $last = end($trace);
        $debugMode = $this->mode === Environment::DEVELOPMENT;
        return [
            'appDir' => isset($trace[1]['file']) ? dirname($trace[1]['file']) : NULL,
            'wwwDir' => isset($last['file']) ? dirname($last['file']) : NULL,
            'debugMode' => $debugMode,
            'productionMode' => !$debugMode,
            'environment' => $debugMode ? 'development' : 'production',
            'consoleMode' => PHP_SAPI === 'cli',
        ];
    }

    /**
     * @return Container
     */
    public function createContainer()
    {
        // Create folder
        $cache = $this->cacheDir . '/PiDi';
        if (!is_dir($cache)) {
            @mkdir($cache, 0777, TRUE);
        }

        // Parameters
        $this->parameters = array_merge($this->getDefaultParameters(), $this->parameters);

        // Build container
        $loader = new ContainerLoader(
            $cache,
            $this->parameters['debugMode']
        );
        $class = $loader->load(
            [$cache, $this->parameters, $this->configs, $this->excludedClasses],
            [$this, 'generateContainer']
        );

        // Create & initialize container
        $container = new $class;
        $container->initialize();
        return $container;
    }

    /**
     * @param Compiler $compiler
     * @return string
     */
    public function generateContainer(Compiler $compiler)
    {
        // Load configs
        $loader = new Loader();
        $compiler->addConfig(['parameters' => $this->parameters]);
        foreach ($this->configs as $config) {
            $compiler->addConfig($loader->load($config));
        }
        $compiler->addDependencies($loader->getDependencies());

        // Exclude classes
        $builder = $compiler->getContainerBuilder();
        $builder->addExcludedClasses($this->excludedClasses);

        // Fire events
        foreach ($this->onCompile as $cb) {
            call_user_func_array($cb, [$compiler]);
        }

        // Compile container
        $classes = $compiler->compile();
        $classes[0]->setExtends(PiDiContainer::class);

        return implode("\n", []) . "\n\n" . implode("\n\n\n", $classes);
    }

}