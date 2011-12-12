<?php

namespace Quice\Validator;

class DateValidator
{

    public function isValid($date)
    {
        $dateArr = explode("-", $date);
        if(!is_array($dateArr)){
            return false;
        }
        if(count($dateArr) != 3) {
            return false;
        }
        if (is_numeric($dateArr[0]) && is_numeric($dateArr[1]) && is_numeric($dateArr[2])) {
            return checkdate($dateArr[1],$dateArr[2],$dateArr[0]);
        }
        return false;
    }

}
