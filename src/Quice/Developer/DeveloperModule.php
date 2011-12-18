<?php

namespace Quice\Developer;

use Quice\Container\Module;

class DeveloperModule extends Module
{
    public function configure()
    {
        $this->bind('IndexAction')->to('Quice\Developer\IndexAction');
        $this->bind('ErrorAction')->to('Quice\Developer\ErrorAction');
        $this->bind('StyleAction')->to('Quice\Developer\StyleAction');
        $this->bind('DocAction')->to('Quice\Developer\DocumentAction');
    }
}

class IndexAction
{
    public $request, $response;

    public function doGet()
    {
        return $this->response->render('developer/welcome');
    }
}

class DocumentAction
{
    public $request, $response;

    public function doGet()
    {
        $content = nl2br(file_get_contents(__DIR__ . '/../../../README.md'));
        return $this->response->render('developer/document', array('content' => $content));
    }
}

class StyleAction
{
    public $request, $response;

    public function doGet()
    {
        $this->response->setHeader('Content-Type', 'text/css');
        return $this->response->render('style');
    }
}

class ErrorAction
{
    public $request, $response;

    public function execute($e)
    {
        return $this->response->render('error', array('e' => $e));
    }
}