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

use PDOException;
use Exception;
use PDO;

class DataAccess
{
    /**
     * Database connection.
     *
     * @var object
     */
    private $connection = null;

    /**
     * Logger
     *
     * @var object
     */
    public $logger = null;

    /**
     * Database transaction.
     *
     * @var boolean
     */
    private $transaction = false;

    /**
     * Auto transaction.
     *
     * @var boolean
     */
    private $autoTransaction = false;

    /**
     * Table prefix.
     *
     * @var string
     */
    public $tablePrefix = '';

    public $dsn = '';
    public $user = null;
    public $password = null;

    /**
     * Constructor.
     *
     */
    public function __construct($dsn = null, $user = null, $password = null)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Connect to a database server.
     * @return void
     */
    public function connect($dsn, $user, $password)
    {
        try {
            $this->connection = new PDO($dsn, $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), intval($e->getCode()));
        }

        if (!$this->connection) {
            throw new Exception('Database Connecting Error');
        }

        $this->connection->exec("SET NAMES 'utf8'");

        return $this->connection;
    }

    /**
     * Check if the database has a connection.
     *
     * @return boolean
     */
    public function hasConnection()
    {
        if ($this->connection) return true;
        return false;
    }

    /**
     * Get database connection.
     *
     * @return object
     */
    public function getConnection()
    {
        if ($this->connection) {
            return $this->connection;
        } else {
            return $this->connect($this->dsn, $this->user, $this->password);
        }
    }

    /**
     * Disconnects from a database.
     *
     * @return void
     */
    public function disconnect()
    {
        $this->connection = null;
    }

    /**
     * Quotes a string for use in a query.
     *
     * @return string
     */
    public function quote($string)
    {
        return $this->getConnection()->quote($string);
    }

    /**
     * Set Logger.
     *
     * @return void
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get Logger.
     *
     * @return object
     */
    public function getLogger()
    {
        if (null === $this->logger) {
            throw new Exception('Undefined logger instance.');
        }

        return $this->logger;
    }

    /**
     * Get query error code.
     *
     * @return integer
     */
    public function errorCode()
    {
        return $this->getConnection()->errorCode();
    }

    /**
     * Get query error info.
     *
     * @return string
     */
    public function errorInfo()
    {
        return $this->getConnection()->errorInfo();
    }

    /**
     * Get columns info.
     *
     * @return array
     */
    public function getColumns($table)
    {
        if(!$this->tableExists($table)) {
            return null;
        }

        $sql = "SHOW COLUMNS FROM ".$table."";
        return $this->findBySql($sql);
    }

    /**
     * Check table if exists.
     *
     * @return boolean
     */
    public function tableExists($table)
    {
        $sql = "SHOW TABLES LIKE '{$table}'";

        return $this->query($sql);
    }

    /**
     * Truncate a database table
     * Should delete all rows and reset SERIAL/AUTO_INCREMENT keys to 0
     */
    public function truncateTable($table)
    {
        $sql = "TRUNCATE TABLE " . $table;

        return $this->query($sql);
    }

    /**
     * Drop a database table
     * Destructive and dangerous - drops entire table and all data
     */
    public function dropTable($table)
    {
        $sql = "DROP TABLE IF EXISTS " . $table;

        return $this->query($sql);
    }

    /**
     * Create a database
      * Will throw errors if user does not have proper permissions
     */
    public function createDatabase($database)
    {
        $sql = "CREATE DATABASE " . $database;

        return $this->query($sql);
    }

    /**
     * Drop a database table
     * Destructive and dangerous - drops entire table and all data
     * Will throw errors if user does not have proper permissions
     */
    public function dropDatabase($database)
    {
        $sql = "DROP DATABASE " . $database;

        return $this->query($sql);
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @return object
     */
    public function prepare($string)
    {
        return $this->getConnection()->prepare($string);
    }

    /**
     * Retrieves the ID generated for an AUTO_INCREMENT column by the previous INSERT query.
     *
     * @return int|FALSE  int on success or FALSE on failure
     */
    public function lastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Set auto start transaction.
     *
     */
    public function setAutoTransaction($autoTranscation = false)
    {
        $this->autoTransaction = $autoTranscation;
    }

    /**
     * Check if has transaction.
     *
     * @return boolean
     */
    public function hasTransaction()
    {
        return $this->transaction;
    }

    /**
     * Begins a transaction (if supported).
     *
     * @param  string  optinal savepoint name
     * @return void
     * @throws Exception
     */
    public function beginTransaction($savepoint = null)
    {
        if (!$this->getConnection()->beginTransaction()) {
            $err = $this->getConnection()->errorInfo();
            throw new Exception("SQLSTATE[$err[0]]: $err[2]", intval($err[1]));
        }
        $this->transaction = true;
    }

    /**
     * Commits statements in a transaction.
     *
     * @param  string  optinal savepoint name
     * @return void
     * @throws Exception
     */
    public function commit($savepoint = null)
    {
        $this->transaction = false;
        if (!$this->getConnection()->commit()) {
            $err = $this->getConnection()->errorInfo();
            throw new Exception("SQLSTATE[$err[0]]: $err[2]", intval($err[1]));
        }
    }

    /**
     * Rollback changes in a transaction.
     *
     * @param  string  optinal savepoint name
     * @return void
     * @throws Exception
     */
    public function rollBack($savepoint = null)
    {
        $this->transaction = false;
        if (!$this->getConnection()->rollBack()) {
            $err = $this->getConnection()->errorInfo();
            throw new Exception("SQLSTATE[$err[0]]: $err[2]", intval($err[1]));
        }
    }

    /**
     * Executes a prepared statement.
     *
     * @return array
     */
    public function query($sql, $params = null)
    {
        $this->getLogger()->debug($sql);
        $this->getLogger()->debug(var_export($params, true));

        if ($this->autoTransaction && !$this->hasTransaction()) {
            $this->beginTransaction();
        }

        $sth = $this->getConnection()->prepare($sql);

        if (is_array($params)) {
            $sth->execute($params);
        } else {
            $sth->execute();
        }

        $rowCount = $sth->rowCount();
        $this->getLogger()->debug('Query result: ' . $rowCount);

        return $rowCount;
    }

    /**
     * Get the next row from a result set.
     *
     * @return array
     */
    public function getBySql($sql, $params = null)
    {
        $this->getLogger()->debug($sql);
        $this->getLogger()->debug(var_export($params, true));

        $sql = $this->applyLimit($sql, 0, 1);
        $sth = $this->getConnection()->prepare($sql);

        if (is_array($params)) {
            $sth->execute($params);
        } else {
            $sth->execute();
        }

        $result = (array) $sth->fetch(PDO::FETCH_ASSOC);
        $sth->closeCursor();

        $this->getLogger()->debug('Result set: ' . var_export($result, true));

        return $result;
    }

    /**
     * Get one result
     *
     * @return array
     */
    public function get($table, $conditions = null, $fields = null, $order = array())
    {
        // Select
        $params = array();
        $sql = sprintf('SELECT %s FROM %s', $this->parseFields($fields), $table);

        // Conditions
        if($conditions) {
            list($conSql, $params) = $this->parseConditions($conditions);
            $sql .= $conSql;
        }

        // Order
        if($order) {
            $sql .= $this->parseOrder($order);
        }

        return $this->getBySql($sql, $params);
    }

    /**
     * Returns an array containing all of the result set rows.
     *
     * @return array
     */
    public function findBySql($sql, $params = null, $index = null, $offset = null)
    {
        $this->getLogger()->debug($sql);
        $this->getLogger()->debug(var_export($params, true));

        $sql = $this->applyLimit($sql, $index, $offset);
        $sth = $this->getConnection()->prepare($sql);

        if (is_array($params)) {
            $sth->execute($params);
        } else {
            $sth->execute();
        }

        $result = (array) $sth->fetchAll(PDO::FETCH_ASSOC);
        $sth->closeCursor();

        $this->getLogger()->debug('Result set: ' . var_export($result, true));

        return $result;
    }

    /**
     * Returns an array containing all of the result set rows
     *
     * @return array
     */
    public function find($table, $conditions, $fields, $order, $index = null, $offset = null)
    {
        // Select
        $params = array();
        $sql = sprintf('SELECT %s FROM %s', $this->parseFields($fields), $table);

        // Conditions
        if($conditions) {
            list($conSql, $params) = $this->parseConditions($conditions);
            $sql .= $conSql;
        }

        // Order
        if($order) {
            $sql .= $this->parseOrder($order);
        }

        return $this->findBySql($sql, $params, $index, $offset);
    }

    /**
     * Count the result set rows.
     *
     * @return int
     */
    public function count($table, $conditions = array())
    {
        $params = array();
        $sql = 'SELECT COUNT(*) AS num FROM ' . $table;

        // Conditions
        if($conditions) {
            list($conSql, $params) = $this->parseConditions($conditions);
            $sql .= $conSql;
        }

        // Get reuslt
        $result = $this->getBySql($sql, $params);
        if(empty($result)) return 0;
        return intval($result['num']);
    }

    /**
     * Checks for a record that matches the specific criteria.
     *
     * @param string Table to check
     * @param array Associative array of field values to match
     * @return boolean True if any matching record exists, false if not
     * <code>$db->exists('mytable', array('fieldName' => 'value'));</code>
     */
    public function exists($table, $conditions)
    {
        $params = array();
        $sql = "SELECT 1 as c FROM {$table}";

        // Conditions
        if($conditions) {
            list($conSql, $params) = $this->parseConditions($conditions);
            $sql .= $conSql;
        }

        $result= $this->getBySql($sql, $params);
        return ($result !== false);
    }

    /**
     * Inserts into the specified table values associated to the key fields
     *
     * @param string The table name
     * @param array An associative array of fields and values to insert
     * @return boolean True on success, false if not
     * <code>$db->insert( 'mytable', array( 'fieldName' => 'value' ) );</code>
     */
    public function insert($table, $fields)
    {
        if (empty($fields)) {
            throw new Exception('The submitted data was invalid.');
        }

        $query = "INSERT INTO {$table} ( ";
        $comma = '';

        $params = array();
        foreach($fields as $field => $value) {
            $this->validateField($field);
            $query.= $comma . $field;
            $comma= ', ';
            $params[]= $value;
        }
        $query.= ' ) VALUES ( ' . trim( str_repeat( '?,', count( $fields ) ), ',' ) . ' );';

        $this->query($query, $params);

        return $this->getConnection()->lastInsertId();
    }

    /**
     * function update
     * Updates any record that matches the specific criteria
     * A new row is inserted if no existing record matches the criteria
     *
     * @param string Table to update
     * @param array Associative array of field values to set
     * @param array Associative array of field values to match
     * @return boolean True on success, false if not
     *
     * <code>$db->update('mytable', array('fieldName' => 'newvalue'), array('fieldName' => 'value'));</code>
     */
    public function update($table, $fields, $conditions)
    {
        if (empty($fields) or !is_array($fields)) {
            throw new Exception('The submitted data was invalid.');
        }

        $sql = "UPDATE {$table} SET";
        $params = array();
        $comma = '';
        foreach($fields as $fieldName => $fieldValue) {
            $this->validateField($fieldName);
            $sql .= $comma . " {$fieldName} = ?";
            $params[] = $fieldValue;
            $comma = ' ,';
        }
        $sql .= ' WHERE 1 = 1 ';

        foreach($conditions as $conditionName => $conditionValue) {
            $this->validateField($conditionName);
            $sql .= "AND {$conditionName} = ? ";
            $params[] = $conditionValue;
        }
        return $this->query($sql, $params);
    }

    /**
     * Deletes any record that matches the specific criteria
     *
     * @param string Table to delete from
     * @param array Associative array of field values to match
     * @return boolean True on success, false if not
     * <code>$db->delete('mytable', array('fieldName' => 'value'));</code>
     */
    public function delete($table, $conditions)
    {
        if(empty($conditions)) {
            return false;
        }
        $sql = "DELETE FROM {$table} WHERE 1 = 1 ";
        $values = array();
        foreach ($conditions as $conditionName => $conditionValue) {
            $this->validateField($conditionName);
            $sql .= "AND {$conditionName} = ? ";
            $values[] = $conditionValue;
        }

        return $this->query($sql, $values);
    }

    /**
     * Injects INDEX/OFFSET to the SQL query.
     *
     * @param  string $sql  The SQL query that will be modified.
     * @param  int $index
     * @param  int $offset
     * @return void
     */
    private function applyLimit($sql, $index, $offset)
    {
        if (empty($index) && empty($offset)) return $sql;

        switch ($this->getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME)) {
        case 'mysql':
            $sql .= ' LIMIT ' . ($index < 0 ? '18446744073709551615' : (int) $index)
                . ($offset > 0 ? ', ' . (int) $offset : '');
            break;

        case 'pgsql':
            if ($index >= 0) $sql .= ' LIMIT ' . (int) $index;
            if ($offset > 0) $sql .= ' OFFSET ' . (int) $offset;
            break;

        case 'sqlite':
        case 'sqlite2':
        case 'oci':
            $sql .= ' LIMIT ' . $index . ($offset > 0 ? ' OFFSET ' . (int) $offset : '');
            break;

        case 'odbc':
        case 'mssql':
            if ($offset < 1) {
                $sql = 'SELECT TOP ' . (int) $index . ' * FROM (' . $sql . ')';
                break;
            }
            //  *intentionally break omitted
        default:
            throw new Exception('PDO or driver does not support applying index or offset.');
        }
        return $sql;
    }

    /**
     * Convert query fields to sql string
     *
     * @return string
     */
    private function parseFields($fields)
    {
        if($fields && is_array($fields)) {
            foreach($fields as $field) {
                $this->validateField($field);
            }
            return implode(', ', $fields);
        }
        return '*';
    }

    /**
     * Parse order.
     *
     * @return string
     */
    private function parseOrder($order)
    {
        if(!is_array($order)) {
            throw new Exception('Order must be array');
        }
        $sql = '';
        // Order
        if ($order) {
            $sql .= ' ORDER BY';
            $sep = ' ';
            foreach($order as $key => $value) {
                $this->validateField($key);
                $orderType = strtoupper(trim($value));
                if(!in_array($orderType, array('ASC', 'DESC'))) {
                    throw new Exception('Unknown order type');
                }
                $sql .= $sep . $key . ' ' . $orderType;
                $sep = ', ';
            }
        }
        return $sql;
    }

    /**
     * Parse conditions.
     *
     * @param array conditions
     * @return boolean True if any matching record exists, false if not
     * <code>
     * $conditions = array(
     *   'username' => $username,
     *   'username:lk' => $username,
     *   'user_id:gt' => '100',
     *   'user_id:lt' => '100',
     *   'user_id:le' => '100',
     *   'user_id:in' => array('100', '101', '102', '103'),
     *);
     * </code>
     */
    private function parseConditions($conditions = array()) {

        $params = array();
        $conditionsSql = ' WHERE 1 = 1';

        if (!$conditions) {
            return array($conditionsSql, $params);
        }

        $operators = array(
            'eq' => ' AND :field = ?',
            'ne' => ' AND :field != ?',

            'ge' => ' AND :field >= ?',
            'gt' => ' AND :field > ?',

            'le' => ' AND :field <= ?',
            'lt' => ' AND :field < ?',

            'in' => ' AND :field IN (:value)',

            'lk' => ' AND :field LIKE ?',

            //'ml' => ' AND MOD(:field, 10) < ?',
            //'bt' => ' AND :field & ? = ?',
        );

        foreach ($conditions as $name => $value) {
            $opKey = 'eq';
            $opValue = $operators['eq'];
            $field = $name;

            if (($pos = strrpos($name, '_')) !== false) {
                $opKey = substr($name, $pos + 1);
                if (array_key_exists($opKey, $operators)) {
                    $field = substr($name, 0, $pos);
                    $opValue = $operators[$opKey];
                }
            }

            $this->validateField($field);
            $opReplace = array(':field' => $field);

            if ($opKey == 'in') {
                if(!is_array($value)) {
                    throw new Exception('You must specified an array value with operator in');
                }
                if (!$value = array_filter(array_unique($value))) {
                    throw new Exception('Invalid value with operator in');
                }
                $inValue = trim(str_repeat('?,', count($value)), ',');
                $opReplace[':value'] = $inValue;
            }

            if ($opKey == 'lk') {
                $value = '%' . $value . '%';
            }

            if ($opKey == 'bt') {
                $value = array($value, $value);
            }

            $conditionsSql .= str_replace(array_keys($opReplace), array_values($opReplace), $opValue);

            if (is_array($value)) {
                foreach($value as $v) {
                    $params[] = $v;
                }
            } else {
                $params[] = $value;
            }
        }

        return array($conditionsSql, $params);
    }

    /**
     * Validate field name.
     *
     * @return void
     */
    private function validateField($name)
    {
        $exp = "/^([a-zA-Z0-9_\-])+$/";
        if(!preg_match($exp, $name)) {
            throw new Exception('Invalid field name: ' . $name);
        }
    }

}
