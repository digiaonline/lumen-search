<?php namespace Nord\Lumen\Search\Formatter;

use Nord\Lumen\Search\Contracts\Formatter as FormatterContract;

class LowercaseFormatter implements FormatterContract
{

    /**
     * @inheritdoc
     * @return string
     */
    public function format($value)
    {
        return strtolower($value);
    }
}
