<?php

namespace Demo\Example;

use Quice\Container\Module;

class ExampleModule extends Module
{
    public function configure()
    {
        $this->bind('IndexAction')->to('Demo\Example\IndexAction');
        $this->bind('ErrorAction')->to('Demo\Example\ErrorAction');
    }
}

class BaseAction
{
    public $request, $response;

    private $currentUser = null;

    public function getCurrentUser()
    {
        if (!$this->currentUser && $token = $this->request->getCookie('token')) {
            $params = array('token' => $token);
            $this->currentUser = $this->service->call('user.auth', $params);
        }

        return $this->currentUser;
    }
}

class IndexAction extends BaseAction
{
    public function doGet()
    {
        return $this->response->render('index');
    }
}

class ErrorAction extends BaseAction
{
    public function doGet()
    {
        var_dump($this->response->exception);
    }
}