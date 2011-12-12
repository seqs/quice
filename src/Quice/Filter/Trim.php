<?php

namespace Quice\Filter;

class TrimFilter
{
    public function filter($string)
    {
        return trim($string);
    }
}
