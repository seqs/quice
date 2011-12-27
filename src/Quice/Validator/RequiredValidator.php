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

class RequiredValidator
{

    public function isValid ($str)
    {
        return ($str !== null && $str !== "");
    }
}
