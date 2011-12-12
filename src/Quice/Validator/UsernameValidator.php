<?php

namespace Quice\Validator;

class UsernameValidator
{
    public function isValid($str)
    {
        $exp = "/^([a-zA-Z0-9])+$/";
        return preg_match($exp, $str);
    }
}
