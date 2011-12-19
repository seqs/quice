<?php

namespace Quice\Template;

use Exception;

class TemplateEngine
{
    public $dir = './tpl';

    public $slots = array();
    private $templates = array();
    private $parents = array();
    private $current = null;
    private $contents = array();
    private $currentFile = null;

    public function set($key, $value = null)
    {
        if(is_array($key)) {
            $this->slots = array_merge($this->slots, $key);
        } else if (is_string($key)) {
            $this->slots[$key] = $value;
        } else {
            throw new Exception('Invalid slots key, must be string.');
        }

        return $this;
    }

    public function get($key, $default = null)
    {
        return isset($this->slots[$key]) ? $this->slots[$key] : $default;
    }

    public function escape($str)
    {
        return htmlspecialchars($str);
    }

    public function iff($t, $a, $b)
    {
        if($t) {
            return $a;
        } else {
            return $b;
        }
    }

    public function odd($key, $odd = 'odd', $even = 'even')
    {
        return (($key + 1) % 2 == 1) ? $odd : $even;
    }

    public function output($name, $default = null)
    {
        echo $this->get($name, $default);
        return $this;
    }

    public function start($name)
    {
        $this->slots[$name] = '';
        ob_start();
    }

    public function stop($name)
    {
        $content = ob_get_contents();
        ob_end_clean();
        $this->slots[$name] = $content;
    }

    public function exists($name)
    {
        if(isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        if (false !== strpos($name, "\0")) {
            throw new Exception('A template name cannot contain NUL bytes.');
        }

        $this->currentFile = $this->dir . '/' . $name . '.phtml';

        if (!file_exists($this->currentFile)) {
            return false;
        }

        return $this->templates[$name] = $this->currentFile;
    }

    public function partial($name)
    {
        if(isset($this->contents[$name])) {
            return $this->contents[$name];
        }

        $__template__ = $name;
        if(!$__file__ = $this->exists($__template__)) {
            throw new Exception('Unable read template: ' . $this->currentFile);
        }

        // Get template file
        extract($this->slots);
        ob_start();

        try {
            include $__file__;
            $__content__ = ob_get_contents();
            ob_end_clean();
        } catch(Exception $e) {
            ob_end_clean();
            throw $e;
        }

        return $this->contents[$__template__] = $__content__;
    }

    /**
     * Decorates the current template with another one.
     *
     * @param string $template  The decorator logical name
     */
    public function extend($template)
    {
        $this->parents[$this->current] = $template;
        return $this;
    }

    public function getChild()
    {
        return $this->get('__child__');
    }

    /**
     * Render
     */
    public function render($name, $slots = array())
    {
        $this->set($slots);
        $this->current = $name;
        $this->parents[$name] = null;
        $content = $this->partial($name);

        // decorator
        if ($this->parents[$name]) {
            $slots['__child__'] = $content;
            $content = $this->render($this->parents[$name], $slots);
        }

        return $content;
    }
}
