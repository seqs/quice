<?php

namespace Quice\Validator;

class MinLengthValidator
{

    public function isValid($str, $length)
    {
        return strlen($str) >= intval($length);
    }
}
