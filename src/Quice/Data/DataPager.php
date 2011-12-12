<?php

namespace Quice\Data;

class DataPager
{
    private $results;
    private $pageSize;

    private $currentPage;
    private $pageIndex;
    private $totalRecords;
    private $totalPages;
    private $pageStart;
    private $pageEnd;

    public function __construct($totalRecords = 0, $currentPage = 1, $pageSize = 20)
    {
        $this->totalRecords = $totalRecords;
        $this->pageSize = $pageSize;
        $this->setCurrentPage($currentPage); // Current page no.
    }

    public function toArray()
    {
        return array(
            'page_size' => $this->pageSize,
            'current_page' => $this->currentPage,
            'page_index' => $this->pageIndex,
            'total_records' => $this->totalRecords,
            'total_pages' => $this->totalPages,
            'page_start' => $this->pageStart,
            'page_end' => $this->pageEnd
        );
    }

    public function getPageIndex()
    {
        return $this->pageIndex;
    }

    private function setCurrentPage($currentPage)
    {
        // Get total pages
        $this->totalPages = ceil($this->totalRecords / $this->pageSize);

        // Set current page
        if ($currentPage >= 1 && $currentPage <= $this->totalPages) {
            $this->currentPage = (int)$currentPage;
        } else {
            $this->currentPage = 1;
        }

        // Set pageIndex
        $this->pageIndex = ($this->currentPage - 1) * $this->pageSize;

        $this->setPageSize();

        return true;
    }

    private function setPageSize()
    {
        $this->pageStart = 1;
        $this->pageEnd = $this->totalPages;
        if($this->totalPages > 10) {
            if($this->currentPage < 10){
                $this->pageStart = 1;
                $this->pageEnd = 10;
            } else {
                $this->pageStart = $this->currentPage - 4;
                $this->pageEnd = $this->currentPage + 5;
                if ($this->pageEnd > $this->totalPages) $this->pageEnd = $this->totalPages;
            }
        }
    }
}
