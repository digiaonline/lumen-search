<?php

namespace Nord\Lumen\Search;

use Nord\Lumen\Core\Exception\InvalidArgument;
use Nord\Lumen\Search\Contracts\SearchAdapter;
use ReflectionClass;

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
     * {@inheritdoc}
     */
    public function run()
    {
        $this->applyFilters();
        $this->applySorts();

        return $this->adapter->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function runWithPagination($pageNumber, $pageSize)
    {
        $this->applyFilters();
        $this->applySorts();

        return $this->adapter->getPartialResult(new Pagination($pageNumber, $pageSize));
    }


    protected function applyFilters()
    {
        foreach ($this->filters as $filter) {
            $property = $filter->getProperty();
            $format = $filter->getFormat();
            $value = $filter->getValue();

            if ($format !== null) {
                $value = $this->adapter->formatValue($format, $value);
            }

            switch ($filter->getType()) {
                case Filter::TYPE_BETWEEN:
                    list($from, $to) = explode(',', $value);
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


    protected function applySorts()
    {
        foreach ($this->sorts as $sort) {
            $this->adapter->applySort($sort->getProperty(), $sort->getDirection());
        }
    }

    /**
     * @param mixed $filters
     *
     * @throws InvalidArgument
     */
    protected function setFilters($filters)
    {
        if (empty($filters)) {
            return;
        }

        if (is_string($filters)) {
            $filters = $this->parser->parse($filters);
        }

        if (!is_array($filters)) {
            throw new InvalidArgument('Search filter is malformed.');
        }

        foreach ($filters as $filter) {
            $this->filters[] = $filter instanceof Filter ? $filter : $this->createFilterFromConfig($filter);
        }
    }

    /**
     * @param array $config
     *
     * @return Filter
     */
    protected function createFilterFromConfig(array $config)
    {
        return (new ReflectionClass(Filter::class))->newInstanceArgs($config);
    }

    /**
     * @param mixed $sorts
     *
     * @throws InvalidArgument
     */
    protected function setSorts($sorts)
    {
        if (empty($sorts)) {
            return;
        }

        if (is_string($sorts)) {
            $sorts = $this->parser->parse($sorts);
        }

        if (!is_array($sorts)) {
            throw new InvalidArgument('Search sort is malformed.');
        }

        foreach ($sorts as $sort) {
            $this->sorts[] = $sort instanceof Sort ? $sort : $this->createSortFromConfig($sort);
        }
    }

    /**
     * @param array $config
     *
     * @return Sort
     */
    protected function createSortFromConfig(array $config)
    {
        return (new ReflectionClass(Sort::class))->newInstanceArgs($config);
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
