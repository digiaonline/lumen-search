<?php namespace Nord\Lumen\Search\Formatter;

use Carbon\Carbon;
use DateTime;
use Nord\Lumen\Search\Contracts\Formatter as FormatterContract;

class DateFormatter implements FormatterContract
{

    /**
     * @inheritdoc
     * @return DateTime
     */
    public function format($value)
    {
        return Carbon::createFromTimestamp($value);
    }
}
