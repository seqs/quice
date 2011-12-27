<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Quice\Validator;

use Exception;

class InValidator
{

    public function isValid ($str, $arrs = array())
    {
        if(!is_array($arrs)) {
            throw new Exception('Invalid second parameters, must be array');
        }
        return in_array($str, $arrs);
    }
}
