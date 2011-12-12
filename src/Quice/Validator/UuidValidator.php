<?php

namespace Quice\Validator;

class UuidValidator
{

    public function isValid($str)
    {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                      '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $str) === 1;
    }

}
