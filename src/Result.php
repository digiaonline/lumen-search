<?php namespace Nord\Lumen\Search;

class Result
{

    /**
     * @var array
     */
    private $results = [];

    /**8
     * @var int
     */
    private $totalCount;

    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var int
     */
    private $pageSize;


    /**
     * Result constructor.
     *
     * @param array    $results
     * @param int|null $totalCount
     * @param int|null $pageNumber
     * @param int|null $pageSize
     */
    public function __construct(array $results, $totalCount = null, $pageNumber = null, $pageSize = null)
    {
        $this->results    = $results;
        $this->totalCount = $totalCount;
        $this->pageNumber = $pageNumber;
        $this->pageSize   = $pageSize;
    }


    /**
     * @return int
     */
    public function calculatePageCount()
    {
        return $this->totalCount > 0 && $this->pageSize > 0 ? ceil($this->totalCount / $this->pageSize) : 1;
    }


    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }


    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount !== null ? $this->totalCount : count($this->results);
    }


    /**
     * @return int
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }


    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }
}
