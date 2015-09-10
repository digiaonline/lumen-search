<?php namespace Nord\Lumen\Search;

use Nord\Lumen\Search\Contracts\SearchAdapter;

class Search
{

    /**
     * @var SearchAdapter
     */
    private $adapter;

    /**
     * @var Filter[]
     */
    private $filters = [];

    /**
     * @var Sort[]
     */
    private $sorts = [];


    /**
     * Search constructor.
     *
     * @param string        $filter
     * @param string        $sort
     * @param SearchAdapter $adapter
     */
    public function __construct($filter, $sort, SearchAdapter $adapter)
    {
        $this->setFilters($filter);
        $this->setSorts($sort);
        $this->setAdapter($adapter);
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->applyFilters();
        $this->applySorts();

        return $this->adapter->getResult();
    }


    /**
     * @inheritdoc
     */
    public function runWithPagination($pageNumber, $pageSize)
    {
        $this->applyFilters();
        $this->applySorts();

        return $this->adapter->getPartialResult(new Pagination($pageNumber, $pageSize));
    }


    /**
     *
     */
    private function applyFilters()
    {
        foreach ($this->filters as $filter) {
            $property = $filter->getProperty();
            $value    = $filter->getValue();

            switch ($filter->getType()) {
                case Filter::TYPE_BETWEEN:
                    list ($from, $to) = explode(',', $value);
                    $this->adapter->applyBetweenFilter($property, $from, $to);
                    break;
                case Filter::TYPE_NOT_EQUALS:
                    $this->adapter->applyNotEqualsFilter($property, $value);
                    break;
                case Filter::TYPE_GREATER_THAN:
                    $this->adapter->applyGreaterThanFilter($property, $value);
                    break;
                case Filter::TYPE_LESS_THAN:
                    $this->adapter->applyLessThanFilter($property, $value);
                    break;
                case Filter::TYPE_GREATER_THAN_OR_EQUALS:
                    $this->adapter->applyGreaterThanOrEqualsFilter($property, $value);
                    break;
                case Filter::TYPE_LESS_THAN_OR_EQUALS:
                    $this->adapter->applyLessThanOrEqualsFilter($property, $value);
                    break;
                case Filter::TYPE_BEGINS_WITH:
                    $this->adapter->applyBeginsWithFilter($property, $value);
                    break;
                case Filter::TYPE_ENDS_WITH:
                    $this->adapter->applyEndsWithFilter($property, $value);
                    break;
                case Filter::TYPE_FREE_TEXT:
                    $this->adapter->applyFreeTextFilter($property, $value);
                    break;
                case Filter::TYPE_EQUALS:
                default:
                    $this->adapter->applyEqualsFilter($property, $value);
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
            $this->adapter->applySort($sort->getProperty(), $sort->getDirection());
        }
    }


    /**
     * @param string $filter
     */
    private function setFilters($filter)
    {
        foreach (Filter::stringToArray($filter) as $property => $value) {
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


    /**
     * @param SearchAdapter $adapter
     */
    private function setAdapter(SearchAdapter $adapter)
    {
        $this->adapter = $adapter;
    }
}
