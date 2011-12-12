<?php

namespace Quice\Container;

/**
 * Module
 *
 * @category   Container
 * @copyright  Copyright (c) 2008 Thomas McKelvey
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @author     Thomas McKelvey (http://github.com/tsmckelvey/yadif/tree/master)
 * @author     Benjamin Eberlei (http://github.com/beberlei/yadif/tree/master)
 */
class Module
{
    /**
     * @var bool
     */
    private $isConfigured = false;

    /**
     * @var Container
     */
    private $container = null;

    /**
     * @var Builder
     */
    private $builder = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->builder = new Builder();
    }

    /**
     * Specify an Interface/Component Name which is used for querying the Container for an Implementation.
     *
     * @param  string $componentName
     * @return Builder
     */
    public function bind($componentName)
    {
        return $this->builder->bind($componentName);
    }

    /**
     * @return array
     */
    final public function getConfig()
    {
        if($this->isConfigured == false) {
            $this->isConfigured = true;
            $this->configure();
        }

        return $this->builder->finalize();
    }

    protected function configure()
    {

    }

    public function getBuilder()
    {
        return $this->builder;
    }

    public function install(Module $module)
    {
        return $this->builder->install($module->getBuilder());
    }

}
