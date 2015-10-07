<?php namespace Nord\Lumen\Search;

use Nord\Lumen\Core\Exception\InvalidArgument;

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
     * @param string $value
     *
     * @throws InvalidArgument
     */
    protected function parseValue($value)
    {
        if ($this->isValueTypePair($value)) {
            $this->handleValueTypePair($value);
        } elseif ($this->isBetween($value)) {
            $this->handleBetween($value);
        } elseif ($this->isValue($value)) {
            $this->handleValue($value);
        } elseif (!empty($value)) {
            throw new InvalidArgument('Filter value is malformed.');
        }
    }


    /**
     * @param $value
     */
    protected function handleValueTypePair($value)
    {
        list ($value, $type) = explode(self::DELIMITER, $value);

        $this->setValue($value);
        $this->setType($type);
    }


    /**
     * @param string $value
     */
    protected function handleBetween($value)
    {
        $this->setValue($value);
        $this->setType(self::TYPE_BETWEEN);
    }


    /**
     * @param string $value
     */
    protected function handleValue($value)
    {
        $this->setValue($value);
        $this->setType(self::TYPE_EQUALS);
    }


    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isValueTypePair($value)
    {
        return is_string($value) && strpos($value, self::DELIMITER) !== false;
    }


    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isBetween($value)
    {
        return is_string($value) && strpos($value, ',') !== false;
    }


    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isValue($value)
    {
        return is_string($value) || is_integer($value);
    }


    /**
     * @param string $property
     *
     * @throws InvalidArgument
     */
    private function setProperty($property)
    {
        if (empty($property)) {
            throw new InvalidArgument('Filter property cannot be empty.');
        }

        $this->property = $property;
    }


    /**
     * @param string $value
     *
     * @throws InvalidArgument
     */
    private function setValue($value)
    {
        if (mb_strlen($value) === 0) {
            throw new InvalidArgument('Filter value cannot be empty.');
        }

        $this->value = $value;
    }


    /**
     * @param string $type
     *
     * @throws InvalidArgument
     */
    private function setType($type)
    {
        if (!in_array($type, $this->validTypes)) {
            throw new InvalidArgument("Filter type '$type' is not supported.");
        }

        $this->type = $type;
    }
}
