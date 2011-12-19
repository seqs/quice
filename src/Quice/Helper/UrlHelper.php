<?php

namespace Quice\Helper;

class UrlHelper
{
    public $request = null;
    public $rewrite = false;

    public function href($module = null, $action = null, $query = array())
    {
        $url = $this->request->getBaseUrl() . '/';

        $query['module'] = $module;
        $query['action'] = $action;

        $seprator = '?';

        foreach ($query as $name => $value) {
            if ($value) {
                $url .= $seprator . $name . '=' . urlencode($value);
                $seprator = '&amp;';
            }
        }

        return $url;
    }
}
