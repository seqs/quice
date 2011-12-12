<?php

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
