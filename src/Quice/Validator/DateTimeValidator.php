<?php

namespace Quice\Validator;

class DateTimeValidator
{

    public function isValid($dateTime)
    {
        $pattern = "/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/";
        if (preg_match($pattern, $dateTime, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }
        return false;
    }

}
