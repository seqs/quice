<?php

namespace Quice\Event;

/**
 * EventDispatcher.
 *
 * @package    Event
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class EventDispatcher
{
    public $listeners = array();
    public $container;

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
        $event = new EventNotifier($name, $params);

        foreach ($this->getListeners($name) as $listener) {
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