<?php

namespace Quice\Validator;

class ValidationState
{
    private $validators = array();
    private $messages = array();
    private $errors = array();

    public function addValidator($name, $type, $validator, $message)
    {
        $this->validators[$name][$type] = $validator;
        $this->messages[$name][$type] = $message;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getMessage($name, $type)
    {
        return isset($this->messages[$name][$type]) ? $this->messages[$name][$type] : 'unknown';
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setError($name, $message)
    {
        $this->errors[$name] = $message;
        return $this;
    }

    public function getError($name)
    {
        return isset($this->errors[$name]) ? $this->errors[$name] : null;
    }

    public function isValid($fields)
    {
        foreach ($this->validators as $name => $validators) {
            foreach ($validators as $type => $validator) {
                if (isset($this->errors[$name])) {
                    continue(2);
                }

                $value = isset($fields[$name]) ? $fields[$name] : null;
                if (!$validator->isValid($value)) {
                    $this->errors[$name] = $this->getMessage($name, $type);
                }
            }
        }

        return count($this->errors) == 0;
    }
}