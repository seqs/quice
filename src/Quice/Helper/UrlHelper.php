<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Quice\Helper;

class UrlHelper
{
    public $request = null;
    public $rewrite = false;

    public function href($module = null, $action = null, $query = array())
    {
        $url = $this->request->getBaseUrl() . '/';

        $query = array_merge(array('module' => $module, 'action' => $action), $query);

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
