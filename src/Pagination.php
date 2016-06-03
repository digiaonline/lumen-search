<?php namespace Nord\Lumen\Search;

use Nord\Lumen\Core\Exception\InvalidArgument;

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
    public function __construct($pageNumber = 1, $pageSize = self::DEFAULT_PAGE_SIZE)
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
     * @param int $pageNumber
     */
    private function setPageNumber($pageNumber)
    {
        if (empty($pageNumber)) {
            throw new InvalidArgument('Pagination page number cannot be empty.');
        }

        if (!is_integer($pageNumber)) {
            throw new InvalidArgument('Pagination page number is malformed.');
        }

        $this->pageNumber = $pageNumber;
    }


    /**
     * @param int $pageSize
     */
    private function setPageSize($pageSize)
    {
        if (empty($pageSize)) {
            throw new InvalidArgument('Pagination page size cannot be empty.');
        }

        if (!is_integer($pageSize)) {
            throw new InvalidArgument('Pagination page size is malformed.');
        }

        $this->pageSize = $pageSize;
    }
}
