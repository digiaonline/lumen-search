<?php namespace Nord\Lumen\Search\Contracts;

use Nord\Lumen\Search\Pagination;

interface SearchAdapter
{

    /**
     * @param string $property
     * @param mixed  $from
     * @param mixed  $to
     */
    public function applyBetweenFilter($property, $from, $to);


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyNotEqualsFilter($property, $value);


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyGreaterThanFilter($property, $value);


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyLessThanFilter($property, $value);


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyGreaterThanOrEqualsFilter($property, $value);


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyLessThanOrEqualsFilter($property, $value);


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyFreeTextFilter($property, $value);


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyBeginsWithFilter($property, $value);


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyEndsWithFilter($property, $value);


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyEqualsFilter($property, $value);


    /**
     * @param string $property
     * @param string $direction
     */
    public function applySort($property, $direction);


    /**
     * @return array
     */
    public function getResult();


    /**
     * @param Pagination $pagination
     */
    public function getPartialResult(Pagination $pagination);
}
