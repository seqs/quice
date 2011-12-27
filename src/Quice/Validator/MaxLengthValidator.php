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

class MaxLengthValidator
{

    public function isValid ($str, $length)
    {
        return strlen($str) <= intval($length);
    }
}
