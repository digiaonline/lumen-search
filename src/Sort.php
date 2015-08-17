<?php namespace Nord\Lumen\Search;

use InvalidArgumentException;

class Sort
{

    const DIRECTION_ASCENDING = 'asc';
    const DIRECTION_DESCENDING = 'desc';

    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $direction;

    /**
     * @var array
     */
    private $validDirections = [self::DIRECTION_ASCENDING, self::DIRECTION_DESCENDING];


    /**
     * Sort constructor.
     *
     * @param string $sort
     */
    public function __construct($sort)
    {
        $this->parseSort($sort);
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
    public function getDirection()
    {
        return $this->direction;
    }


    /**
     * @param string $property
     */
    private function setProperty($property)
    {
        $this->property = $property;
    }


    /**
     * @param string $direction
     */
    private function setDirection($direction)
    {
        if (! in_array($direction, $this->validDirections)) {
            throw new InvalidArgumentException("Sort direction '$direction' is not supported.");
        }

        $this->direction = $direction;
    }


    /**
     * @param string $value
     */
    private function parseSort($value)
    {
        if ($this->isPropertyAndDirectionPair($value)) {
            $this->handlePropertyAndDirectionPair($value);
        } elseif ($this->isProperty($value)) {
            $this->handleProperty($value);
        } elseif (!empty($value)) {
            throw new InvalidArgumentException('Sort value is malformed.');
        }
    }


    /**
     * @param string $value
     */
    private function handlePropertyAndDirectionPair($value)
    {
        list ($property, $direction) = explode(':', $value);

        $this->setProperty($property);
        $this->setDirection($direction);
    }


    /**
     * @param string $value
     */
    private function handleProperty($value)
    {
        $this->setProperty($value);
        $this->setDirection(self::DIRECTION_ASCENDING);
    }


    /**
     * @param string $value
     *
     * @return bool
     */
    private function isPropertyAndDirectionPair($value)
    {
        return strpos($value, ':') !== false;
    }


    /**
     * @param string $value
     *
     * @return bool
     */
    private function isProperty($value)
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
        return strpos($string, ',') !== false ? explode(',', $string) : [$string];
    }
}
