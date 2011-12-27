<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Quice\Developer;

use Quice\Container\Module;

class DeveloperModule extends Module
{
    public function configure()
    {
        $this->bind('IndexAction')->to('Quice\Developer\IndexAction');
        $this->bind('ErrorAction')->to('Quice\Developer\ErrorAction');
        $this->bind('DocAction')->to('Quice\Developer\DocumentAction')
            ->property('parser', 'MarkdownParser');

        $this->bind('MarkdownParser')->to('Quice\Markdown\MarkdownParser');
    }
}

class BaseAction
{
    public $request, $response;
}

class IndexAction extends BaseAction
{
    public function doGet()
    {
        return $this->response->render('quice:welcome');
    }
}

class DocumentAction extends BaseAction
{
    public $parser;

    public function doGet()
    {
        $name = $this->request->getQuery('name');
        $baseDir = __DIR__ . '/../../../../';
        $file = $baseDir . ($name ? 'doc/' . basename($name) . '.md' : 'README.md');
        $text = file_exists($file) ? file_get_contents($file) : 'Document not found.';
        $content = $this->parser->transform($text);
        return $this->response->render('quice:document', array('content' => $content));
    }
}

class ErrorAction extends BaseAction
{
    public function execute($e)
    {
        return $this->response->render('quice:error', array('e' => $e));
    }
}