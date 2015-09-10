<?php namespace Nord\Lumen\Search;

use InvalidArgumentException;

class Filter
{

    const SEPARATOR = '|';
    const DELIMITER = ':';

    const TYPE_EQUALS                 = 'eq';
    const TYPE_NOT_EQUALS             = 'neq';
    const TYPE_GREATER_THAN           = 'gt';
    const TYPE_LESS_THAN              = 'lt';
    const TYPE_GREATER_THAN_OR_EQUALS = 'gte';
    const TYPE_LESS_THAN_OR_EQUALS    = 'lte';
    const TYPE_BEGINS_WITH            = 'bw';
    const TYPE_ENDS_WITH              = 'ew';
    const TYPE_FREE_TEXT              = 'ft';
    const TYPE_BETWEEN                = 'bt';

    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $validTypes = [
        self::TYPE_EQUALS,
        self::TYPE_NOT_EQUALS,
        self::TYPE_GREATER_THAN,
        self::TYPE_LESS_THAN,
        self::TYPE_GREATER_THAN_OR_EQUALS,
        self::TYPE_LESS_THAN_OR_EQUALS,
        self::TYPE_BEGINS_WITH,
        self::TYPE_ENDS_WITH,
        self::TYPE_FREE_TEXT,
        self::TYPE_BETWEEN,
    ];


    /**
     * Filter constructor.
     *
     * @param string $property
     * @param string $value
     */
    public function __construct($property, $value)
    {
        $this->setProperty($property);
        $this->parseValue($value);
    }


    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }


    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @param string $property
     */
    private function setProperty($property)
    {
        if (empty($property)) {
            throw new InvalidArgumentException('Filter property cannot be empty.');
        }

        $this->property = $property;
    }


    /**
     * @param string $value
     */
    private function setValue($value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Filter value cannot be empty.');
        }

        $this->value = $value;
    }


    /**
     * @param string $type
     */
    private function setType($type)
    {
        if (!in_array($type, $this->validTypes)) {
            throw new InvalidArgumentException("Filter type '$type' is not supported.");
        }

        $this->type = $type;
    }


    /**
     * @param string $value
     */
    private function parseValue($value)
    {
        if ($this->isValueTypePair($value)) {
            $this->handleValueTypePair($value);
        } elseif ($this->isBetween($value)) {
            $this->handleBetween($value);
        } elseif ($this->isValue($value)) {
            $this->handleValue($value);
        } elseif (!empty($value)) {
            throw new InvalidArgumentException('Filter value is malformed.');
        }
    }


    /**
     * @param $value
     */
    private function handleValueTypePair($value)
    {
        list ($value, $type) = explode(self::DELIMITER, $value);

        $this->setValue($value);
        $this->setType($type);
    }


    /**
     * @param string $value
     */
    private function handleBetween($value)
    {
        $this->setValue($value);
        $this->setType(self::TYPE_BETWEEN);
    }


    /**
     * @param string $value
     */
    private function handleValue($value)
    {
        $this->setValue($value);
        $this->setType(self::TYPE_EQUALS);
    }


    /**
     * @param string $value
     *
     * @return bool
     */
    private function isValueTypePair($value)
    {
        return is_string($value) && strpos($value, self::DELIMITER) !== false;
    }


    /**
     * @param string $value
     *
     * @return bool
     */
    private function isBetween($value)
    {
        return is_string($value) && strpos($value, ',') !== false;
    }


    /**
     * @param string $value
     *
     * @return bool
     */
    private function isValue($value)
    {
        return is_string($value);
    }


    /**
     * @param string $string
     *
     * @return array
     */
    public static function stringToArray($string)
    {
        $array = [];

        if (!empty($string)) {
            $filters = strpos($string, self::SEPARATOR) !== false ? explode(self::SEPARATOR, $string) : [$string];

            foreach ($filters as $string) {
                list($property, $value) = explode(self::DELIMITER, $string, 2);

                $array[$property] = $value;
            }
        }

        return $array;
    }
}
