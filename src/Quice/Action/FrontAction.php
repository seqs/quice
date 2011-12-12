<?php

namespace Quice\Action;

use Exception;

class FrontAction
{
    public $container, $request, $response, $router;

    public function execute()
    {
        try {
            $module = $this->request->getQuery('module', 'core');
            $action = $this->request->getQuery('action', 'index');
            $this->dispatch($module, $action);
        } catch (Exception $e) {
            // $this->response->setContext('exception', $e);
            $this->response->exception = $e;
            $this->dispatch('core', 'error');
        }
    }

    private function dispatch($module, $action)
    {
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
        $this->response->context = $actionClass;
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
