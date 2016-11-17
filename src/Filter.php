<?php namespace Nord\Lumen\Search;

use Nord\Lumen\Search\Exceptions\InvalidArgument;

class Filter
{
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
     * @var string
     */
    private $format;

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
     * @param string      $property
     * @param string      $value
     * @param string      $type
     * @param string|null $format
     */
    public function __construct($property, $value, $type = self::TYPE_EQUALS, $format = null)
    {
        $this->setProperty($property);
        $this->setValue($value);
        $this->setType($type);
        $this->setFormat($format);
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
     * @return string|null
     */
    public function getFormat()
    {
        return $this->format;
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


    /**
     * @param string|null $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }
}
