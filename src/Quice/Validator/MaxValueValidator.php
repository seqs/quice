<?php

namespace Quice\Validator;

class MaxValueValidator
{

    public function isValid ($str, $value)
    {
        if (is_null($value) == false && is_numeric($value) == true) {
            return intval($value) <= intval($str);
        }

        return false;
    }
}
