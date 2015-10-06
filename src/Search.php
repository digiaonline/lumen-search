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
     * @var Configuration
     */
    private $configuration;


    /**
     * Search constructor.
     *
     * @param mixed         $filters
     * @param mixed         $sorts
     * @param SearchAdapter $adapter
     * @param array         $config
     */
    public function __construct($filters, $sorts, SearchAdapter $adapter, array $config = [])
    {
        $this->setConfiguration(new Configuration($config));
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
     * @param $string
     *
     * @return array
     * @throws InvalidArgument
     */
    private function parseFilterString($string)
    {
        if (!is_string($string)) {
            throw new InvalidArgument('Search filter is malformed.');
        }

        return $this->parseString($string);
    }


    /**
     * @param $string
     *
     * @return array
     * @throws InvalidArgument
     */
    private function parseSortString($string)
    {
        if (!is_string($string)) {
            throw new InvalidArgument('Search sort is malformed.');
        }

        return $this->parseString($string);
    }


    /**
     * @param string $string
     *
     * @return array
     */
    private function parseString($string)
    {
        $array = [];

        if (mb_strlen($string)) {
            $separator = $this->configuration->getSeparator();
            $delimiter = $this->configuration->getDelimiter();
            $items     = strpos($string, $separator) !== false ? explode($separator, $string) : [$string];

            foreach ($items as $item) {
                list($property, $value) = explode($delimiter, $item, 2);

                $array[$property] = $value;
            }
        }

        return $array;
    }


    /**
     * @param mixed $filters
     */
    private function setFilters($filters)
    {
        if (!is_array($filters)) {
            $filters = $this->parseFilterString($filters);
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
    private function setSorts($sorts)
    {
        if (!is_array($sorts)) {
            $sorts = $this->parseSortString($sorts);
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
     * @param Configuration $configuration
     */
    private function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }
}
