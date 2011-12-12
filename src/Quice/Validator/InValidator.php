<?php

namespace Quice\Validator;

use Exception;

class InValidator
{

    public function isValid ($str, $arrs = array())
    {
        if(!is_array($arrs)) {
            throw new Exception('Invalid second parameters, must be array');
        }
        return in_array($str, $arrs);
    }
}
