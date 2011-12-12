<?php

namespace Quice\Template;

class TemplateEngine
{
    public $dir = './tpl';
    public $html, $form, $url, $trans, $context;

    private $vars = array();
    private $templates = array();
    private $parents = array();
    private $current = null;
    private $contents = array();
    private $currentFile = null;

    public function set($key, $value = null)
    {
        if(is_array($key)) {
            $this->vars = array_merge($this->vars, $key);
        } else if (is_string($key)) {
            $this->vars[$key] = $value;
        } else {
            throw new Exception('Invalid vars key, must be string.');
        }

        return $this;
    }

    public function get($key, $default = null)
    {
        return isset($this->vars[$key]) ? $this->vars[$key] : $default;
    }

    public function escape($var)
    {
        return htmlspecialchars($var);
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
        $this->vars[$name] = '';
        ob_start();
    }

    public function stop($name)
    {
        $content = ob_get_contents();
        ob_end_clean();
        $this->vars[$name] = $content;
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
        extract($this->vars);
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
    public function render($name, $vars = array())
    {
        $this->set($vars);
        $this->current = $name;
        $this->parents[$name] = null;
        $content = $this->partial($name);

        // decorator
        if ($this->parents[$name]) {
            $vars['__child__'] = $content;
            $content = $this->render($this->parents[$name], $vars);
        }

        return $content;
    }
}
