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

use Exception;

class DataObject
{
    public $dataAccess = null;
    public $table = null;
    public $primary = 'id';

    public function getPrimary()
    {
        return $this->primary;
    }

    public function getTable()
    {
        if (null === $this->table) {
            throw new Exception('Table name undefined.');
        }

        return $this->table;
    }

    public function getDataAccess()
    {
        if (null === $this->dataAccess) {
            throw new Exception('Data access undefined.');
        }
        return $this->dataAccess;
    }

    /**
     * Find first record matching given conditions
     *
     * @param array $conditions Array of conditions in column => value pairs
     */
    final public function get($conditions = array(), $fields = array(), $order = array())
    {
        return $this->getDataAccess()->get($this->getTable(), $conditions, $fields, $order);
    }

    /**
     * Insert record
     *
     * @param none
     */
    final public function create($fields)
    {
        return $this->getDataAccess()->insert($this->getTable(), $fields);
    }

    /**
     * Update records matching given conditions
     *
     * @param array $conditions Array of conditions in column => value pairs
     */
    final public function update($fields, $conditions)
    {
        return $this->getDataAccess()->update($this->getTable(), $fields, $conditions);
    }

    /**
     * Delete records matching given conditions
     *
     * @param array $conditions Array of conditions in column => value pairs
     */
    final public function delete($conditions = array())
    {
        return $this->getDataAccess()->delete($this->getTable(), $conditions);
    }

    /**
     * Check exists matching given conditions
     *
     * @param array $conditions Array of conditions in column => value pairs
     */
    final public function exists($conditions = array())
    {
        return $this->getDataAccess()->exists($this->getTable(), $conditions);
    }

    /**
     * Count records matching given conditions
     *
     * @param array $conditions Array of conditions in column => value pairs
     */
    final public function count($conditions = null)
    {
        return $this->getDataAccess()->count($this->getTable(), $conditions);
    }

    /**
     * Load record from primary key
     */
    final public function getByPrimary($primaryValue, $fields = array())
    {
        return $this->get(array($this->getPrimary() => $primaryValue), $fields);
    }

    /**
     * Update records by primary key
     *
     * @param mix $primaryValue primary key value
     */
    final public function updateByPrimary($primaryValue)
    {
        return $this->update(array($this->getPrimary() => $primaryValue));
    }

    /**
     * Delete records by primary key
     *
     * @param array $primaryValue Primary key value
     */
    final public function deleteByPrimary($primaryValue)
    {
        return $this->delete(array($this->getPrimary() => $primaryValue));
    }

    /**
     * Check exists matching records by primary key
     *
     * @param array $primaryValue Primary key value
     */
    final public function existsByPrimary($primaryValue)
    {
        return $this->exists(array($this->getPrimary() => $primaryValue));
    }

    /**
     * Find records with given conditions
     * If all parameters are empty, find all records
     *
     * @param array $conditions Array of conditions in column => value pairs
     */
    final public function find($conditions = null, $fields = null,
        $order = null, $index = 0, $offset = 100)
    {
        if (empty($order)) {
            $order = array($this->getPrimary() => 'desc');
        }

        return $this->getDataAccess()->find($this->getTable(), $conditions,
            $fields, $order, $index, $offset);
    }


    /**
     * Find records with given conditions by page
     * If all parameters are empty, find all records
     *
     * @param array $conditions Array of conditions in column => value pairs
     */
    final public function findByPage($conditions = null, $fields = null,
        $order = null, $currentPage = 1, $perPage = 20)
    {
        // Count records
        $totalRecords = $this->count($conditions);
        if (empty($totalRecords)) {
            return array('params' => array(), 'records' => array());
        }

        // Validate page size
        $perPage = empty($perPage) ? 20 : $perPage;

        if ($perPage > 10000) {
            throw new Exception('Per page limit 10000');
        }

        // Pager
        $pager = new DataPager($totalRecords, $currentPage, $perPage);

        return array(
            'params' => $pager->toArray(),
            'records' => $this->find($conditions, $fields, $order, $pager->getPageIndex(), $perPage)
        );
    }

    /**
     * Find records with given primary key data
     *
     */
    final public function findByPrimaries($primaries = array(), $fields = null)
    {
        $records = array();
        $primaries = array_filter(array_unique($primaries));

        if ($primaries > 10000) {
            throw new Exception('Primaries limit 10000');
        }

        // Get records
        $conditions = array($this->getPrimary() => $primaries);
        $data = $this->getDataAccess()->find($this->getTable(), $fields, $conditions);
        foreach ($data as $item) {
            $records[$item[$this->getPrimary()]] = $item;
        }

        return $records;
    }

    /**
     * Find the records by reference records with given fields.
     *
     * @param array $records The records
     *
     * $fields = array('creator', 'updator');
     * $userDao->findByRecords($records, $fields);
     */
    final public function findByRecords($records, $fields)
    {
        if (empty($records)) {
            return array();
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }

        // Get primaries
        $primaries = array();
        foreach ($records as $record) {
            foreach ($fields as $field) {
                if (isset($record[$field])) {
                    $primaries[] = $record[$field];
                }
            }
        }

        return $this->findByPrimaries($primaries);
    }

}
