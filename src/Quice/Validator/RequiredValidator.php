<?php

namespace Quice\Validator;

class RequiredValidator
{

    public function isValid ($str)
    {
        return ($str !== null && $str !== "");
    }
}
