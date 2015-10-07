<?php namespace Nord\Lumen\Search;

use Nord\Lumen\Core\Exception\InvalidArgument;
use Nord\Lumen\Search\Contracts\SearchAdapter;

class Search
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
     * @var SearchAdapter
     */
    private $adapter;

    /**
     * @var StringParser
     */
    private $parser;


    /**
     * Search constructor.
     *
     * @param mixed         $filters
     * @param mixed         $sorts
     * @param SearchAdapter $adapter
     * @param StringParser  $parser
     */
    public function __construct($filters, $sorts, SearchAdapter $adapter, StringParser $parser = null)
    {
        $this->setParser($parser !== null ? $parser : new StringParser());
        $this->setFilters($filters);
        $this->setSorts($sorts);
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
    protected function applyFilters()
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
    protected function applySorts()
    {
        foreach ($this->sorts as $sort) {
            $this->adapter->applySort($sort->getProperty(), $sort->getDirection());
        }
    }


    /**
     * @param mixed $filters
     */
    protected function setFilters($filters)
    {
        if (empty($filters)) {
            return;
        }

        if (!is_array($filters)) {
            $filters = $this->parser->parse($filters);
        }

        foreach ($filters as $property => $value) {
            if (!empty($value)) {
                $this->filters[$property] = $value instanceof Filter ? $value : new Filter($property, $value);
            }
        }
    }


    /**
     * @param mixed $sorts
     */
    protected function setSorts($sorts)
    {
        if (empty($sorts)) {
            return;
        }

        if (!is_array($sorts)) {
            $sorts = $this->parser->parse($sorts);
        }

        foreach ($sorts as $property => $value) {
            if (!empty($value)) {
                $this->sorts[$property] = $value instanceof Sort ? $value : new Sort($property, $value);
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


    /**
     * @param StringParser $parser
     */
    private function setParser(StringParser $parser)
    {
        $this->parser = $parser;
    }
}
