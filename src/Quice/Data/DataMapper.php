<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
