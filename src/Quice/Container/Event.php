<?php

namespace Quice\Container;

class Event
{
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
        foreach ($this->getListeners($name) as $listener) {
            call_user_func($listener, $event);
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