<?php

namespace Quice\Validator;

class MaxLengthValidator
{

    public function isValid ($str, $length)
    {
        return strlen($str) <= intval($length);
    }
}
