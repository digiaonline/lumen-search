<?php namespace Nord\Lumen\Search;

use Nord\Lumen\Search\Contracts\SearchAdapter as SearchAdapterContract;

abstract class SearchAdapter implements SearchAdapterContract
{

    /**
     * @var Filter[]
     */
    private $filters = [];

    /**
     * @var Sort[]
     */
    private $sorts = [];


    /**
     * SearchAdapter constructor.
     *
     * @param array  $filters
     * @param string $sort
     */
    public function __construct(array $filters, $sort)
    {
        $this->setFilters($filters);
        $this->setSorts($sort);
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->applyFilters();
        $this->applySorts();

        return $this->getResult();
    }


    /**
     * @inheritdoc
     */
    public function runWithPagination($pageNumber, $pageSize)
    {
        $this->applyFilters();
        $this->applySorts();

        return $this->getPartialResult(new Pagination($pageNumber, $pageSize));
    }


    /**
     *
     */
    private function applyFilters()
    {
        foreach ($this->filters as $filter) {
            $property = $filter->getProperty();
            $value    = $filter->getValue();
            $type     = $filter->getType();

            switch ($type) {
                case Filter::TYPE_BETWEEN:
                    list ($from, $to) = explode(',', $value);
                    $this->applyBetweenFilter($property, $from, $to);
                    break;
                case Filter::TYPE_NOT_EQUALS:
                    $this->applyNotEqualsFilter($property, $value);
                    break;
                case Filter::TYPE_GREATER_THAN:
                    $this->applyGreaterThanFilter($property, $value);
                    break;
                case Filter::TYPE_LESS_THAN:
                    $this->applyLessThanFilter($property, $value);
                    break;
                case Filter::TYPE_GREATER_THAN_OR_EQUALS:
                    $this->applyGreaterThanOrEqualsFilter($property, $value);
                    break;
                case Filter::TYPE_LESS_THAN_OR_EQUALS:
                    $this->applyLessThanOrEqualsFilter($property, $value);
                    break;
                case Filter::TYPE_FREE_TEXT:
                    $this->applyFreeTextFilter($property, $value);
                    break;
                case Filter::TYPE_EQUALS:
                default:
                    $this->applyEqualsFilter($property, $value);
                    break;
            }
        }
    }


    /**
     *
     */
    private function applySorts()
    {
        foreach ($this->sorts as $sort) {
            $this->applySort($sort->getProperty(), $sort->getDirection());
        }
    }


    /**
     * @param array $filters
     */
    private function setFilters(array $filters)
    {
        foreach ($filters as $property => $value) {
            if (!empty($value)) {
                $this->filters[] = new Filter($property, $value);
            }
        }
    }


    /**
     * @param string $sort
     */
    private function setSorts($sort)
    {
        foreach (Sort::stringToArray($sort) as $value) {
            if (!empty($value)) {
                $this->sorts[] = new Sort($value);
            }
        }
    }
}
