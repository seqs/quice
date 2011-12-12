<?php

namespace Quice\Validator;

class CodeValidator
{
    public function isValid($str)
    {
        $exp = "/^([a-zA-Z0-9_\-\.])+$/";
        return preg_match($exp, $str);
    }
}
