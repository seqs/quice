<?php

namespace Quice\Container;

/**
 * Builder
 *
 * @category   Container
 * @copyright  Copyright (c) 2008 Thomas McKelvey
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @author     Thomas McKelvey (http://github.com/tsmckelvey/yadif/tree/master)
 * @author     Benjamin Eberlei (http://github.com/beberlei/yadif/tree/master)
 */
class Builder
{
    /**
     * @var array
     */
    private $config = array();

    /**
     * @var string
     */
    private $lastComponentName = null;

    /**
     * @return array
     */
    public function finalize()
    {
        return $this->config;
    }

    /**
     * Specify an Interface/Component Name which is used for querying the Container for an Implementation.
     *
     * @param  string $componentName
     * @return Builder
     */
    public function bind($componentName)
    {
        $this->lastMethodKey = null;
        $this->lastComponentName = $componentName;
        $this->config[$componentName] = array('class' => $componentName);
        return $this;
    }

    /**
     * Specify concrete implementation class name of the previously given Interface/Component Name
     *
     * @param  string $componentName
     * @return Builder
     */
    public function to($className)
    {
        $this->config[$this->lastComponentName]['class'] = $className;
        return $this;
    }

    /**
     * Specifiy Callback which creates a concrete implementation of the previously given Interface/Component Name
     *
     * @param  callback $factoryCallback
     * @return Builder
     */
    public function toProvider($factoryCallback)
    {
        $this->config[$this->lastComponentName]['factory'] = $factoryCallback;
        return $this;
    }

    /**
     * Bind Parameter Name to a specific value
     *
     * @param  string $paramName
     * @param  mixed $paramValue
     * @return Builder
     */
    public function param($paramName, $paramValue)
    {
        if(!isset($this->config[$this->lastComponentName]['params'])) {
            $this->config[$this->lastComponentName]['params'] = array();
        }
        $this->config[$this->lastComponentName]['params'][$paramName] = $paramValue;
        return $this;
    }

    /**
     * Specify properties that are given to the class of the chosen implementation.
     *
     * Parameters are only allowed to be given in the format ':paramName' and then be specified
     * by the {@link param()} method.
     *
     * @param  string $propertyName
     * @param  mixed $propertyValue
     * @return Builder
     */
    public function property($propertyName, $propertyValue)
    {
        if(!isset($this->config[$this->lastComponentName]['properties'])) {
            $this->config[$this->lastComponentName]['properties'] = array();
        }
        $this->config[$this->lastComponentName]['properties'][$propertyName] = $propertyValue;
        return $this;
    }

    /**
     * Specify the scope of the implementation 'singleton' or 'prototype'.
     *
     * @param  string $scope
     * @return Builder
     */
    public function scope($scope)
    {
        $this->config[$this->lastComponentName]['scope'] = $scope;
        return $this;
    }

    /**
     *
     * @param  string $decoratorComponentName
     * @return Builder
     */
    public function decorateWith($decoratorComponentName)
    {
        if (!isset($this->config[$this->lastComponentName]['decorateWith'])) {
            $this->config[$this->lastComponentName]['decorateWith'] = array();
        }
        $this->config[$this->lastComponentName]['decorateWith'][] = $decoratorComponentName;
        return $this;
    }

    /**
     *
     * @param  object Builder $builder
     * @return void
     */
    public function install(Builder $builder)
    {
        $this->config = array_merge($this->config, $builder->finalize());
        return $this;
    }

    /**
     * Match the component name by given pattern.
     *
     * @param  string $pattern
     * @return Builder
     */
    public function match($pattern)
    {
        $this->config[$this->lastComponentName]['match'] = $pattern;
        return $this;
    }

}
