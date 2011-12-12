<?php

namespace Quice\Helper;

class HtmlHelper
{

    private $crumbs = array();
    public $trans = null;
    public $url = null;

    public function addCrumb($label, $url = null, $options = array(), $level = 0)
    {
        $crumb = array();
        $crumb['label'] = $label;
        $crumb['url'] = $url;
        $crumb['options'] = $options;
        if ($level) {
            $this->crumbs[$level] = $crumb;
        } else {
            $this->crumbs[] = $crumb;
        }
        return $this;
    }

    public function hasCrumbs()
    {
        return !empty($this->crumbs);
    }

    public function getCrumbs($home = 'Home', $separator = '&nbsp;&raquo;&nbsp;')
    {
        if(empty($this->crumbs)) {
            return null;
        }

        ksort($this->crumbs);
        $out = array();
        $out[] = $this->link($this->url->href(), $home);

        foreach ($this->crumbs as $crumb) {
            if ($crumb['url'] != null) {
                $url = $this->url->href($crumb['url']);
                $out[] = $this->link($url, $crumb['label'], null, $crumb['options']);
            } else {
                $out[] = $crumb['label'];
            }
        }

        return implode($separator, $out);
    }

    public function charset($charset = "utf-8")
    {
        return sprintf($this->tags['charset'], (!empty($charset) ? $charset : 'utf-8'));
    }

    public function style($data, $oneline = true)
    {
        if (!is_array($data)) {
            return $data;
        }
        $out = array();
        foreach ($data as $key=> $value) {
            $out[] = $key.':'.$value.';';
        }
        if ($oneline) {
            return implode(' ', $out);
        }
        return implode("\n", $out);
    }

    public function div($text = null, $class = null, $options = array())
    {
        if (!empty($class)) {
            $options['class'] = $class;
        }
        return $this->tag('div', $text, $options);
    }

    public function p($text, $class = null, $options = array())
    {
        if (!is_null($class)) {
            $options['class'] = $class;
        }
        return $this->tag('p', $text, $options);
    }

    public function link($href, $text, $class = null, $options = array())
    {
        $options['href'] = $href;
        if (!is_null($class)) {
            $options['class'] = $class;
        }
        return $this->tag('a', $text, $options);
    }

    public function span($text, $class = null, $options = array())
    {
        if (!is_null($class)) {
            $options['class'] = $class;
        }
        return $this->tag('span', $text, $options);
    }

    public function tag($name, $text = null, $options = array())
    {
        if (!is_array($options)) {
            $options = array('class' => $options);
        }
        $options = $this->parseAttributes($options);
        if(!is_null($text)) {
            $format = '<%s%s>%s</%s>';
            return sprintf($format, $name, $options, $text, $name);
        } else {
            $format = '<%s%s />';
            return sprintf($format, $name, $options);
        }
    }

    private function parseAttributes($options = array())
    {
        if(empty($options)) {
            return '';
        }

        $attributes = array();
        $format = '%s="%s"';
        foreach($options as $key => $value) {
            if($value) {
                $attributes[] = sprintf($format, $key, $value);
            }
        }
        return ' ' . implode(' ', $attributes);
    }

    public function renderError($error, $class = "error")
    {
        if (empty($error)) {
            return '';
        }

        return $this->span($error->getMessage(), $class);
    }

}
