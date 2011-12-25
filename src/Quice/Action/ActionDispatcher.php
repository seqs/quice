<?php

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
        $module = $this->request->getQuery('module', 'core');
        $action = $this->request->getQuery('action', 'index');
        $moduleName = $this->camelize($module) . 'Module';
        $actionName = $this->camelize($action) . 'Action';

        if (!$this->container->hasComponent($moduleName)) {
            throw new Exception('Module not found: ' . $module, 404);
        }

        $moduleClass = $this->container->getComponent($moduleName);
        $this->container->addComponents($moduleClass->getConfig());

        if (!$this->container->hasComponent($actionName)) {
            throw new Exception('Action not found: ' . $action, 404);
        }

        $actionClass = $this->container->getComponent($actionName);

        $methods = array('doGet', 'doPost', 'doPut', 'doDelete');
        $method = 'do' . ucfirst(strtolower($this->request->getMethod()));

        if (!in_array($method, $methods)) {
            throw new Exception('Method Not Allowed', 501);
        }

        if (!method_exists($actionClass, $method)) {
            throw new Exception('Not Implemented', 501);
        }

        $actionClass->request = $this->request;
        $actionClass->response = $this->response;
        $actionClass->$method();
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
