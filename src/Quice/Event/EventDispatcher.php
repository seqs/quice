<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Quice\Event;

/**
 * EventDispatcher.
 *
 * @package    Event
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class EventDispatcher
{
    public $container;
    private $listeners = array();

    public function connect($name, $listener)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = array();
        }

        $this->listeners[$name][] = $listener;
    }

    public function disconnect($name, $listener)
    {
        if (!isset($this->listeners[$name])) {
            return false;
        }

        foreach ($this->listeners[$name] as $i => $callable) {
            if ($listener === $callable) {
                unset($this->listeners[$name][$i]);
            }
        }
    }

    public function notify($name, $params = array())
    {
        $events = $this->container->getEvents();
        $this->listeners = array_merge($this->listeners, $events);

        $event = new EventNotifier($name, $params);

        foreach ($this->getListeners($name) as $listener) {
            list($listenerName, $listenerMethod) = $listener;
            $listenerClass = $this->container->getComponent($listenerName);
            $event = $listenerClass->$listenerMethod($event);
            if (!$event->isProcessed()) {
                break;
            }
        }

        return $event;
    }

    public function getListeners($name)
    {
        if (!isset($this->listeners[$name])) {
            return array();
        }

        return $this->listeners[$name];
    }

    public function hasListeners($name)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = array();
        }

        return (boolean) count($this->listeners[$name]);
    }

}