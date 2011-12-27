<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Quice\Container;

use Exception;

/**
 * Injector
 *
 * @category   Container
 * @copyright  Copyright (c) 2008 Thomas McKelvey
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @copyright  Copyright (c) 2011 sunseesiu
 * @author     Thomas McKelvey (http://github.com/tsmckelvey/yadif/tree/master)
 * @author     Benjamin Eberlei (http://github.com/beberlei/yadif/tree/master)
 * @author     sunseesiu (http://github.com/sunseesiu/quice/tree/master)
 */
class Injector
{
    const CHAR_CONFIG_VALUE = '%';
    const CHAR_PARAM_VALUE = ':';

    /**
     * Identifier for singleton scope
     */
    const SCOPE_SINGLETON = "singleton";

    /**
     * Identifier for prototype scope (new object each call)
     */
    const SCOPE_PROTOTYPE = "prototype";

    /**
     * Class index key of component $config
     */
    const CONFIG_CLASS = 'class';

    /**
     * Properties
     */
    const CONFIG_PROPERTIES = 'properties';

    /**
     * Parameters
     */
    const CONFIG_PARAMETERS = 'params';

    /**
     * Singleton index key of component $config
     */
    const CONFIG_SCOPE = 'scope';

    /**
     * Events
     */
    const CONFIG_EVENTS = 'events';

    /**
     * Factory Config key for classes that are instantiated via a static factory method
     */
    const CONFIG_FACTORY = 'factory';

    const CONFIG_DECORATED_WITH = 'decorateWith';

    /**
     * container of component configurations
     *
     * @var array
     */
    protected $container = array();

    /**
     * parameters which have been set, expected to be bound
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * All managed instances inside this container which are not Scoped "Prototype"
     *
     * @var array
     */
    protected $instances = array();

    /**
     * Config
     *
     * @var null
     */
    protected $config = null;

    /**
     * Current components
     *
     * @var array
     */
    private $currentComponents = array();

    /**
     * Events
     *
     * @var array
     */
    protected $events = array();

    /**
     * Construct Dependency Injection Container
     *
     * @param array $components
     * @param array $config
     */
    public function __construct($components = array(), $config = array())
    {
        $this->addComponents($components);
        $this->setConfig($config);
    }

    /**
     * Set Config object
     *
     * @param  array $config
     * @return Container
     */
    public function setConfig($config = array())
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Getter method for internal array of component configurations
     *
     * @return array
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Getter method for internal array of parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get currently managed instances of the container.
     *
     * In its current containerations only the singleton scoped objects are returned,
     * but in an containeration with additional session scope it is necessary to return
     * also those from this function.
     *
     * @return array
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * Get Config inside this Container
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get events inside this Container
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Merge two Containers
     *
     * @todo   Handle duplicates, currently array_merge overwrites them
     * @param  Container $container
     * @return Container
     */
    public function merge(Injector $container)
    {
        $this->container = array_merge($this->container, $container->getContainer());
        $this->instances = array_merge($this->instances, $container->getInstances());

        $otherConfig = $container->getConfig();
        $ownConfig = $this->getConfig();
        $this->setConfig(array_merge($ownConfig, $otherConfig));

        $this->events = array_merge($this->events, $container->getEvents());

        return $this;
    }

    /**
     * Add several components to the container via a config.
     *
     * @param array $components
     * @return Container
     */
    public function addComponents($components)
    {
        if (isset($components) && is_array($components)) {
            foreach ($components as $componentName => $componentConfig) {
                $this->addComponent($componentName, $componentConfig);
            }
        }
    }

    /**
     * Add a component to the injector
     *
     * If $config is omitted, $name is assumed to be the classname.
     *
     * @param string|Injector $name Class-tag, class, or Injector
     * @param array $config Array of configuration values
     * @return Container
     */
    public function addComponent($name = null, array $config = array())
    {
        if ($name instanceof Injector) { // if Injector
            $this->merge($name);
        } elseif(is_string($name)) {
            if (!is_array($config) || !isset($config[self::CONFIG_CLASS])) { // assume name is the class name
                $config[self::CONFIG_CLASS] = $name;
            }

            if (!isset($config[self::CONFIG_PROPERTIES]) || !is_array($config[self::CONFIG_PROPERTIES])) {
                $config[ self::CONFIG_PROPERTIES ] = array();
            }

            if(!isset($config[self::CONFIG_PARAMETERS])) {
                $config[self::CONFIG_PARAMETERS] = array();
            }

            // check for singleton config parameter and set it to true as default if not found.
            if(!isset($config[self::CONFIG_SCOPE])) {
                $config[self::CONFIG_SCOPE] = self::SCOPE_SINGLETON;
            }

            // Register events
            if(isset($config[self::CONFIG_EVENTS])) {
                foreach ((array)$config[self::CONFIG_EVENTS] as $eventName => $methodName) {
                    if (!isset($this->events[$eventName])) {
                        $this->events[$eventName] = array();
                    }
                    $this->events[$eventName][] = array($name, $methodName);
                }
            }

            $name = strtolower($name);
            $this->container[$name] = $config;
        } else {
            throw new Exception('$string not string|Injector, is ' . gettype($name));
        }

        return $this;
    }

    /**
     * Bind a parameter
     *
     * @param string $param The parameter name, to be given with a leading colon ":param"
     * @param mixed $value The value to bind to the parameter
     * @return Container
     */
    public function bindParam($param, $value)
    {
        if (!is_string($param)) {
            throw new Exception('$param not string, is ' . gettype($param));
        }

        if ($param[0] != ':') {
            throw new Exception($param . ' must start with a colon (:)');
        }

        $this->parameters[$param] = $value;

        return $this;
    }

    /**
     * Retrieve a parameter by name
     *
     * @param  mixed $param Retrieve named parameter
     * @param  string $component
     * @param  string $method
     * @return mixed
     */
    public function getParam($param, $component=null)
    {
        $component = strtolower($component);
        if(isset($this->container[$component])) {
            $component = $this->container[$component];
            if(isset($component[self::CONFIG_PARAMETERS][$param])) {
                return $component[self::CONFIG_PARAMETERS][$param];
            }
        }

        if(isset($this->parameters[$param])) {
            return $this->parameters[$param];
        } else {
            return null;
        }
    }

    /**
     * Bind multiple parameters by way of array
     * @param array $params Array of parameters, key as param to bind, value as the bound value
     * @return Container
     */
    public function bindParams($params = null)
    {
        if (!is_array($params)) {
            throw new Exception('$params must be array, is ' . gettype($params));
        }

        foreach ($params as $param => $value) {
            $this->bindParam($param, $value);
        }

        return $this;
    }

    /**
     * Get several components at once.
     *
     * @param  array $components
     * @return array
     */
    public function getComponents(array $components)
    {
        foreach ($components as $k =>$value) {
            $components[$k] = $this->getComponent($value);
        }
        return $components;
    }

    /**
     * Clear a current instance.
     *
     * @param string $name
     */
    public function clear($name)
    {
        unset($this->instances[$name]);
    }

    /**
     * Check if component exists.
     *
     * @param  string $name
     * @return boolean
     */
    public function hasComponent($name)
    {
        return array_key_exists(strtolower($name), $this->container);
    }

    /**
     * Get component class name.
     *
     * @param  string $name
     * @return string
     */
    public function getComponentClass($name)
    {
        if (!$this->hasComponent($name)) {
            throw new Exception("Component '".$name."' does not exist in container.");
        }

        $component = $this->container[strtolower($name)];
        return $component[self::CONFIG_CLASS];
    }

    /**
     * Get back a fully assembled component based on the configuration provided beforehand
     *
     * @param  string $name The name of the component
     * @return mixed
     */
    public function getComponent($name)
    {
        if (!is_string($name)) {
            return $name;
        }

        $origName = $name;
        $name = strtolower($name);

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if ($name === "thiscontainer") {
            return $this;
        } elseif($name === "clonecontainer") {
            return clone $this;
        } elseif(!array_key_exists($name, $this->container)) {
            throw new Exception("Component '".$origName."' does not exist in container.");
        }

        if (isset($this->currentComponents[$name]) && $this->currentComponents[$name]) {
            throw new Exception("Get component failed, may be caused by loop.");
        } else {
            $this->currentComponents[$name] = true;
        }

        $component = $this->container[$name];
        $scope = $component[self::CONFIG_SCOPE];

        // if class is set and doesn't exist
        if (!class_exists($component[self::CONFIG_CLASS])) {
            throw new Exception('Class ' . $component[self::CONFIG_CLASS] . ' not found');
        }

        // Create new instance
        $class = $component[self::CONFIG_CLASS];
        $properties = $component[self::CONFIG_PROPERTIES];
        $componentInstance = new $class();
        foreach ($properties as $propertyName => $propertyValue) {
            if (!property_exists($componentInstance, $propertyName)) {
                throw new Exception('Undefined property: ' . $class . '::$' . $propertyName);
            }
            $componentInstance->$propertyName = $this->injectParameter($propertyValue, $name);
        }

        if (isset($component[self::CONFIG_DECORATED_WITH])) {
            foreach ($component[self::CONFIG_DECORATED_WITH] as $decoratorComponent) {
                $this->instances['decoratedinstance'] = $componentInstance;
                $componentInstance = $this->getComponent($decoratorComponent);
            }
            unset($this->instances['decoratedinstance']);
        }

        if ($scope !== self::SCOPE_PROTOTYPE) {
            $this->instances[$name] = $componentInstance;
        }

        $this->currentComponents[$name] = false;

        return $componentInstance;
    }

    protected function injectParameter($propertyValue, $component)
    {
        if (is_array($propertyValue)) {
            $value = array();
            foreach ($propertyValue as $k => $v) {
                $value[$k] = $this->injectParameter($v, $component);
            }
        } elseif (substr($propertyValue, 0, 1) == self::CHAR_CONFIG_VALUE
                && substr($propertyValue, -1) == self::CHAR_CONFIG_VALUE) {
            $value = $this->getConfigValue($propertyValue);
        } elseif (substr($propertyValue, 0, 1) == self::CHAR_PARAM_VALUE) {
            $value =  $this->getParam($propertyValue, $component);
        } else {
            $value = $this->getComponent($propertyValue);
        }

        return $value;
    }

    /**
     * Given an accessor specification %config.foo.bar% traverse the config and return value.
     *
     * @param  string $accessor
     * @return mixed
     */
    protected function getConfigValue($accessor)
    {
        if ($this->config === null) {
            throw new Exception("A config value '".$accessor."' is required but no config was given!");
        }

        $accessor = substr($accessor, 1, strlen($accessor)-2);

        $parts = explode(".", $accessor);
        $current = $this->config;
        for ($i = 0; $i < count($parts); $i++) {
            $current = isset($current[$parts[$i]]) ? $current[$parts[$i]] : null;
        }
        return $current;
    }
}
