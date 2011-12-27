<?php

/*
 * This file is part of the Quice framework.
 *
 * (c) sunseesiu@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Quice\Data;

class DataPager
{
    private $results;
    private $perPage;

    private $currentPage;
    private $pageIndex;
    private $totalRecords;
    private $totalPages;
    private $pageStart;
    private $pageEnd;

    public function __construct($totalRecords = 0, $currentPage = 1, $perPage = 20)
    {
        $this->totalRecords = $totalRecords;
        $this->perPage = $perPage;
        $this->setCurrentPage($currentPage); // Current page no.
    }

    public function toArray()
    {
        return array(
            'per_page' => $this->perPage,
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
        $this->totalPages = ceil($this->totalRecords / $this->perPage);

        // Set current page
        if ($currentPage >= 1 && $currentPage <= $this->totalPages) {
            $this->currentPage = (int)$currentPage;
        } else {
            $this->currentPage = 1;
        }

        // Set pageIndex
        $this->pageIndex = ($this->currentPage - 1) * $this->perPage;

        $this->setPerPage();

        return true;
    }

    private function setPerPage()
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
