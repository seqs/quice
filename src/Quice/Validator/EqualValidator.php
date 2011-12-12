<?php

namespace Quice\Validator;

class EqualValidator
{

    public function isValid($str, $equal)
    {
        return ($str == $equal);
    }

}
