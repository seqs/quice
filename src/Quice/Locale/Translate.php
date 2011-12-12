<?php

namespace Quice\Locale;

class Translate
{
    public $dir = './i18n';
    private $locale = 'en_US';

    private $data = array();
    private $files = array();
    private $languages = array();

    public function load($name)
    {
        if (isset($this->files[$name])) {
            return $this;
        } else {
            $this->files[$name] = $this->dir . '/' . $this->getLocale() . '/' . $name . '.php';
        }

        if (file_exists($this->files[$name])) {
            $this->data[$name] = include($this->files[$name]);
        }

        return $this;
    }

    public function setTimeZone($timeZone = 'UTC')
    {
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($timeZone);
        }
    }

    public function setAcceptLanguages($languages = array())
    {
        $this->languages = $languages;
    }

    public function setLocale($locale)
    {
        if ($locale == 'auto') {
            $acceptLanguage = key($this->languages);
            if(!empty($acceptLanguage)) $this->locale = $acceptLanguage;
        } else {
            $this->locale = $locale;
        }
        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getText($messageId, $name)
    {
        if (isset($this->data[$name][$messageId])) {
            return $this->data[$name][$messageId];
        }

        $this->load($name);

        if (isset($this->data[$name][$messageId])) {
            return $this->data[$name][$messageId];
        } else {
            return $this->readable($messageId);
        }
    }

    private function readable($messageId)
    {
        return ucfirst(str_replace('_', ' ', $messageId));
    }

}
