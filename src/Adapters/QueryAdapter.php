<?php namespace Nord\Lumen\Search\Adapters;

use Doctrine\ORM\QueryBuilder;
use Nord\Lumen\Search\Formatter\Factory;
use Nord\Lumen\Search\Pagination;
use Nord\Lumen\Search\Result;
use Nord\Lumen\Search\Contracts\SearchAdapter as SearchAdapterContract;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class QueryAdapter implements SearchAdapterContract
{

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var string
     */
    private $tableAlias;


    /**
     * QueryAdapter constructor.
     *
     * @param QueryBuilder $queryBuilder
     * @param string       $tableAlias
     * @param Factory      $formatter
     */
    public function __construct(QueryBuilder $queryBuilder, $tableAlias, Factory $formatter)
    {
        $this->queryBuilder = $queryBuilder;
        $this->tableAlias   = $tableAlias;
        $this->formatter    = $formatter;
    }


    /**
     * @param string $format
     * @param mixed  $value
     *
     * @return mixed
     */
    public function formatValue($format, $value)
    {
        return $this->formatter->create($format)->format($value);
    }


    /**
     * @param string $property
     * @param mixed  $from
     * @param mixed  $to
     */
    public function applyBetweenFilter($property, $from, $to)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property BETWEEN :{$property}_from AND :{$property}_to")
            ->setParameters(["{$property}_from" => $from, "{$property}_to" => $to]);
    }


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyNotEqualsFilter($property, $value)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property != :$property")
            ->setParameter($property, "%$value%");
    }


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyGreaterThanFilter($property, $value)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property > :$property")
            ->setParameter($property, $value);
    }


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyLessThanFilter($property, $value)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property < :$property")
            ->setParameter($property, $value);
    }


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyGreaterThanOrEqualsFilter($property, $value)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property >= :$property")
            ->setParameter($property, $value);
    }


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyLessThanOrEqualsFilter($property, $value)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property <= :$property")
            ->setParameter($property, $value);
    }


    /**
     * @inheritdoc
     */
    public function applyBeginsWithFilter($property, $value)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property LIKE :$property")
            ->setParameter($property, "%$value");
    }


    /**
     * @inheritdoc
     */
    public function applyEndsWithFilter($property, $value)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property LIKE :$property")
            ->setParameter($property, "$value%");
    }


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyFreeTextFilter($property, $value)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property LIKE :$property")
            ->setParameter($property, "%$value%");
    }


    /**
     * @param string $property
     * @param mixed  $value
     */
    public function applyEqualsFilter($property, $value)
    {
        $this->queryBuilder
            ->andWhere("$this->tableAlias.$property = :$property")
            ->setParameter($property, $value);
    }


    /**
     * @inheritdoc
     */
    public function applySort($property, $direction)
    {
        $this->queryBuilder
            ->addOrderBy("$this->tableAlias.$property", $direction);
    }


    /**
     * @inheritdoc
     */
    public function getResult()
    {
        return new Result($this->queryBuilder->getQuery()->getResult());
    }


    /**
     * @inheritdoc
     */
    public function getPartialResult(Pagination $pagination)
    {
        $pager = new DoctrineORMAdapter($this->queryBuilder);

        $pageSize = $pagination->getPageSize();

        return new Result(
            $pager->getSlice($pagination->calculateOffset(), $pageSize)->getArrayCopy(),
            $pager->getNbResults(),
            $pagination->getPageNumber(),
            $pageSize
        );
    }
}
