<?php

namespace Quice\Validator;

class EmailValidator
{
    public function isValid ($str)
    {
        $exp = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9])+(\.[a-zA-Z0-9_-]+)+$/";
        return preg_match($exp, $str);
    }
}
