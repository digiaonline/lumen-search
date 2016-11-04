<?php

namespace Nord\Lumen\Search\Contracts;

interface Formatter
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function format($value);
}
