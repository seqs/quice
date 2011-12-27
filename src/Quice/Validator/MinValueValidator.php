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

class MinValueValidator
{

    public function isValid ($str, $value)
    {
      if (is_null($value) == false && is_numeric($value)) {
          return intval($value) >= intval($str);
      }

      return false;
    }
}
