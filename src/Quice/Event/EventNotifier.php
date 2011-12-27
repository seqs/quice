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

use ArrayAccess;
use InvalidArgumentException;

/**
 * EventNotifier.
 *
 * @package    Event
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class EventNotifier implements ArrayAccess
{
    protected
      $value      = null,
      $processed  = false,
      $name       = '',
      $params = array();

    /**
     * Constructs a new EventListener.
     *
     * @param string  $name         The event name
     * @param array   $params   An array of params
     */
    public function __construct($name, $params = array())
    {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * Returns the event name.
     *
     * @return string The event name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the return value for this event.
     *
     * @param mixed $value The return value
     */
    public function setReturnValue($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the return value.
     *
     * @return mixed The return value
     */
    public function getReturnValue()
    {
        return $this->value;
    }

    /**
     * Sets the processed flag.
     *
     * @param Boolean $processed The processed flag value
     */
    public function setProcessed($processed)
    {
        $this->processed = (boolean) $processed;
    }

    /**
     * Returns whether the event has been processed by a listener or not.
     *
     * @return Boolean true if the event has been processed, false otherwise
     */
    public function isProcessed()
    {
        return $this->processed;
    }

    /**
     * Returns the event params.
     *
     * @return array The event params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Returns the event param by name.
     *
     * @return mix The event param value
     */
    public function getParam($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Returns true if the parameter exists (implements the ArrayAccess interface).
     *
     * @param  string  $name  The parameter name
     *
     * @return Boolean true if the parameter exists, false otherwise
     */
    public function offsetExists($name)
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * Returns a parameter value (implements the ArrayAccess interface).
     *
     * @param  string  $name  The parameter name
     *
     * @return mixed  The parameter value
     */
    public function offsetGet($name)
    {
        if (!array_key_exists($name, $this->params)) {
            throw new InvalidArgumentException(sprintf('The event "%s" has no "%s" parameter.', $this->name, $name));
        }

        return $this->params[$name];
    }

    /**
     * Sets a parameter (implements the ArrayAccess interface).
     *
     * @param string  $name   The parameter name
     * @param mixed   $value  The parameter value
     */
    public function offsetSet($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Removes a parameter (implements the ArrayAccess interface).
     *
     * @param string $name    The parameter name
     */
    public function offsetUnset($name)
    {
        unset($this->params[$name]);
    }
}