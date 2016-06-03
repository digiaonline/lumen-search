<?php namespace Nord\Lumen\Search;

use Nord\Lumen\Core\Exception\InvalidArgument;

class Sort
{
    const DIRECTION_ASCENDING  = 'asc';
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
     * @param string $property
     * @param string $direction
     */
    public function __construct($property, $direction)
    {
        $this->setProperty($property);
        $this->setDirection($direction);
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
        if (!in_array($direction, $this->validDirections)) {
            throw new InvalidArgument("Sort direction '$direction' is not supported.");
        }

        $this->direction = $direction;
    }
}
