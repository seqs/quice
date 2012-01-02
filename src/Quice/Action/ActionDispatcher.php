<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Quice\Action;

use Exception;

class ActionDispatcher
{
    public $container, $request, $response, $router;

    public function execute()
    {
        try {
            $this->dispatch();
        } catch (Exception $e) {
            $this->error($e);
        }
    }

    private function error($e)
    {
        $moduleClass = $this->container->getComponent('ErrorModule');
        $this->container->addComponents($moduleClass->getConfig());
        $errorAction = $this->container->getComponent('ErrorAction');
        $errorAction->request = $this->request;
        $errorAction->response = $this->response;
        $errorAction->execute($e);
    }

    private function dispatch()
    {
        $module = $this->request->getQuery('module', 'index');
        $action = $this->request->getQuery('action', 'index');
        $do = $this->request->getQuery('do', 'index');
        $moduleName = $this->camelize($module) . 'Module';
        $actionName = $this->camelize($action) . 'Action';
        $doName = 'do' . $this->camelize($do);

        if (!$this->container->hasComponent($moduleName)) {
            throw new Exception('Module Not Found: ' . $module, 404);
        }

        $moduleClass = $this->container->getComponent($moduleName);
        $this->container->addComponents($moduleClass->getConfig());

        if (!$this->container->hasComponent($actionName)) {
            throw new Exception('Action Not Found: ' . $action, 404);
        }

        $actionClass = $this->container->getComponent($actionName);

        if (!method_exists($actionClass, $doName)) {
            throw new Exception('Method Not Found: ' . $do, 404);
        }

        $actionClass->request = $this->request;
        $actionClass->response = $this->response;
        $actionClass->$doName();
    }

    private function camelize($name)
    {
        $search = array('/', '_'); $replace = array(' / ', ' '); $newreplace = array('.', '');
        return str_replace($replace, $newreplace, ucwords(str_replace($search, $replace, $name)));
    }

    public function setErrorHandler()
    {
        set_error_handler(array($this, 'errorHandler'));
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
