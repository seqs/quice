<?php

namespace Quice\Data;

class DataMapper extends ArrayIterator
{

    public function set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    public function get($offset)
    {
        $value = $this->offsetGet($offset);

        if (is_array($value)) {
            return new self($value);
        }

        return $value;
    }

}
