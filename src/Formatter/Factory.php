<?php namespace Nord\Lumen\Search\Formatter;

use Nord\Lumen\Core\Exception\InvalidArgument;
use Nord\Lumen\Search\Contracts\Formatter;

class Factory
{

    const FORMAT_DATE = 'date';

    /**
     * @var array
     */
    private static $map = [
        self::FORMAT_DATE => 'Nord\Lumen\Search\Formatter\DateFormatter',
    ];


    /**
     * @param string $format
     *
     * @return Formatter
     */
    public function create($format)
    {
        if (!isset(self::$map[$format])) {
            throw new InvalidArgument("Formatter '$format' is not supported.");
        }

        return new self::$map[$format];
    }
}
