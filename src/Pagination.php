<?php namespace Nord\Lumen\Search;

class Pagination
{

    const DEFAULT_PAGE_SIZE = 20;

    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var int
     */
    private $pageSize;


    /**
     * Pagination constructor.
     *
     * @param int $pageNumber
     * @param int $pageSize
     */
    public function __construct($pageNumber, $pageSize)
    {
        $this->setPageNumber($pageNumber);
        $this->setPageSize($pageSize);
    }


    /**
     * @return int
     */
    public function calculateOffset()
    {
        return ($this->pageNumber * $this->pageSize) - $this->pageSize;
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


    /**
     * @param $pageNumber
     */
    private function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber !== null ? (int)$pageNumber : 1;
    }


    /**
     * @param $pageSize
     */
    private function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize !== null ? (int)$pageSize : self::DEFAULT_PAGE_SIZE;
    }

}
