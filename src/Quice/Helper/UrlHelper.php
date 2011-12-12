<?php

namespace Quice\Helper;

class UrlHelper
{
    public $request = null;
    public $rewrite = false;

    public function href($module = null, $action = null, $query = array())
    {
        $url = $this->request->getBaseUrl() . '/';

        if (!$module) {
            return $url;
        } else {
            $url .= $this->rewrite ? $module : '?router=' . $action;
        }

        if ($this->rewrite) {
            $seprator = '?';
        } else {
            $seprator = '&amp;';
        }

        foreach ($query as $name => $value) {
            if ($value) {
                $url .= $seprator . $name . '=' . urlencode($value);
                $seprator = '&amp;';
            }
        }

        return $url;
    }
}
