<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Quice\Demo;

use Quice\Container\Module;

class DemoModule extends Module
{
    public function configure()
    {
        $this->bind('IndexAction')->to('Quice\Demo\IndexAction');
        $this->bind('EventAction')->to('Quice\Demo\EventAction')
            ->property('fooService', 'FooService');

        $this->bind('FooService')->to('Quice\Demo\FooService')
            ->property('eventDispatcher', 'EventDispatcher');

        $this->bind('BarService')->to('Quice\Demo\BarService')
            ->on('foo.before_send', 'onFooBeforeSend')
            ->on('foo.after_send', 'onFooAfterSend');
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
    public function doIndex()
    {
        return $this->response->render('index');
    }
}

class EventAction extends BaseAction
{
    public $fooService;

    public function doIndex()
    {
        $ret = $this->fooService->send('tom', 'jerry');
        return $this->response->render('event', array('ret' => $ret));
    }
}

class FooService
{
    public $eventDispatcher;

    public function send($foo, $bar)
    {
        $ret = array();

        $eventNotifier = $this->eventDispatcher->notify('foo.before_send', array('foo' => $foo));
        $ret[] = $eventNotifier->getReturnValue();

        $eventNotifier = $this->eventDispatcher->notify('foo.after_send', array('bar' => $bar));
        $ret[] = $eventNotifier->getReturnValue();

        return $ret;
    }
}

class BarService
{
    public function onFooBeforeSend($eventNotifier)
    {
        $foo = $eventNotifier->getParam('foo');
        $eventNotifier->setReturnValue('before param: ' . $foo);
        return $eventNotifier;
    }

    public function onFooAfterSend($eventNotifier)
    {
        $bar = $eventNotifier->getParam('bar');
        $eventNotifier->setReturnValue('after param: ' . $bar);
        return $eventNotifier;
    }
}
