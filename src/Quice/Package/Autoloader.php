<?php

namespace Quice\Package;

use Exception;

class Autoloader
{
    private $packages = array();

    public function register($packages)
    {
        $this->packages = $packages;
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array($this, 'autoload'));
    }

    public function autoload($class)
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return true;
        }

        $ds = DIRECTORY_SEPARATOR;

        $file = str_replace('/' , $ds, $this->getDir($class))
            . $ds . str_replace('\\', $ds, $class) . '.php';

        if (file_exists($file)) {
            require $file;
        } else {
            throw new Exception('Class not found: ' . $class);
        }

        return true;
    }

    public function getDir($class)
    {
        $names = explode('\\', $class);

        foreach ($names as $name) {
            array_pop($names);
            $package = implode('\\', $names);
            if (isset($this->packages[$package])) {
                return trim($this->packages[$package]);
            }
        }

        throw new Exception('Package not found with class: ' . $class);
    }

}
