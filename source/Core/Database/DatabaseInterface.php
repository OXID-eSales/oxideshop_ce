<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */
namespace OxidEsales\Eshop\Core\Database;

use object_ADOConnection;
use object_ResultSet;
use pear_ADOConnection;

/**
 * The database connection interface specifies how a database connection should look and act.
 */
interface DatabaseInterface
{

    /** The default fetch mode as implemented by the DBAL */
    const FETCH_MODE_DEFAULT = 0;
    /** Fetch the query result into an array with integer keys */
    const FETCH_MODE_NUM = 1;
    /** Fetch the query result into an array with string keys */
    const FETCH_MODE_ASSOC = 2;
    /** Fetch the query result into a mixed array with both integer and string keys */
    const FETCH_MODE_BOTH = 3;

    /**
     * Setter for the database connection.
     *
     * @param mysql_driver|mysql_extend|mysql_meta|mysqli_driver|mysqli_extend|mysqli_extra|object_ADOConnection|pear_ADOConnection $connection The connection to the database.
     */
    public function setConnection($connection);

    /**
     * Set the fetch mode of the database connection and return the previous fetch mode.
     *
     * @param int $fetchMode See DatabaseInterface::FETCH_MODE_* for valid values
     *
     * @return int The previous fetch mode.
     */
    public function setFetchMode($fetchMode);

    /**
     * Return connection to the database.
     *
     * @param bool $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @return mysql_driver|mysql_extend|mysql_meta|mysqli_driver|mysqli_extend|mysqli_extra|object_ADOConnection|pear_ADOConnection The connection to the database.
     */
    public function getDb($executeOnSlave = true);

    /**
     * Get one column, which you have to give into the sql select statement, of the first row, corresponding to the
     * given sql statement.
     *
     * @param string     $sqlSelect      The sql select statement
     * @param array|bool $parameters     Array of parameters, for the given sql statement.
     * @param bool       $executeOnSlave Should the given sql statement executed on the slave?
     *
     * @return string The first column of the first row, which is fitting to the given sql select statement.
     */
    public function getOne($sqlSelect, $parameters = false, $executeOnSlave = true);

    /**
     * Get one row.
     *
     * @param string     $query          The sql statement we want to execute.
     * @param array|bool $parameters     The parameters array.
     * @param bool       $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @return array
     */
    public function getRow($query, $parameters = false, $executeOnSlave = true);

    /**
     * Get all values as array. Alias of getArray.
     *
     * @param string $query          The sql statement we want to execute.
     * @param array  $parameters     The parameters array.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @return array                 May be associative, indexed or mixed array depending on the given fetch mode
     */
    public function getAll($query, $parameters = array(), $executeOnSlave = true);

    /**
     * Get all values as array.
     *
     * @param string $query          The sql statement we want to execute.
     * @param array  $parameters     The parameters array.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @return array                 May be associative, indexed or mixed array depending on the given fetch mode
     */
    public function getArray($query, $parameters = array(), $executeOnSlave = true);

    /**
     * Get value
     *
     * @param string     $query          The sql statement we want to execute.
     * @param array|bool $parameters     The parameters array.
     * @param bool       $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @return mixed|Object_ResultSet
     */
    public function select($query, $parameters = false, $executeOnSlave = true);

    /**
     * Get values as an associative array. The first column is taken as the key for the row.
     *
     * @param string     $query          The sql statement we want to execute.
     * @param array|bool $parameters     The parameters array.
     * @param bool       $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @return array The rows for the corresponding sql select statement. The first column value is the key for the row.
     */
    public function getAssoc($query, $parameters = false, $executeOnSlave = true);

    /**
     * Get the values of a column.
     *
     * @param string     $query          The sql statement we want to execute.
     * @param array|bool $parameters     The parameters array.
     * @param bool       $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @todo: What kind of array do we expect numeric or assoc? Does it depends on FETCH_MODE?
     *
     * @return array The values of a column of a corresponding sql query.
     */
    public function getCol($query, $parameters = false, $executeOnSlave = true);

    /**
     * Run a given select sql statement with a limit clause on the database.
     *
     * @param string     $query      The sql statement we want to execute.
     * @param int        $limit      Number of rows to select
     * @param int        $offset     Number of rows to skip
     * @param array|bool $parameters The parameters array.
     * @param bool       $type       Connection type
     *
     * @return mixed|Object_ResultSet The result of the given query.
     */
    public function selectLimit($query, $limit = -1, $offset = -1, $parameters = false, $type = true);

    /**
     * Executes query and returns result set.
     *
     * @param string     $query      The sql statement we want to execute.
     * @param array|bool $parameters The parameters array.
     *
     * @return mixed|Object_ResultSet
     */
    public function execute($query, $parameters = false);

    /**
     * Execute the given query and return the corresponding result set.
     *
     * @param string     $query      The sql statement(s) we want to run.
     * @param array|bool $parameters The parameters array.
     *
     * @return mixed|Object_ResultSet The result set of the execution of the given sql statement(s).
     */
    public function query($query, $parameters = false);

    /**
     * Get the number of rows, which where changed during the last sql statement.
     *
     * @return int The number of rows affected by the last sql statement.
     */
    public function affected_rows();

    /**
     * Get the last error number, occurred while executing a sql statement through any of the methods in this class.
     *
     * @return int The last mysql error number.
     */
    public function errorNo();

    /**
     * Get the last error message, occurred while executing a sql statement through any of the methods in this class.
     *
     * @return string The last error message.
     */
    public function errorMsg();

    /**
     * Quote the given string.
     *
     * @param string $value The string we want to quote.
     *
     * @return string The given string in quotes.
     */
    public function qstr($value);

    /**
     * Quote the given string. Same as qstr.
     *
     * @param string $value The string we want to quote.
     *
     * @return string The given string in quotes.
     */
    public function quote($value);

    /**
     * Quote every string in the given array.
     *
     * @param array $arrayOfStrings The strings to quote as an array.
     *
     * @return array The given strings quoted.
     */
    public function quoteArray($arrayOfStrings);

    /**
     * Return meta data for the columns.
     *
     * @param string $table The name of the table.
     *
     * @return array The meta information about the columns.
     */
    public function metaColumns($table);

    /**
     * Returns meta data.
     *
     * @todo: deprecate as fast as possible, it doesn't run on modern php versions.
     *
     * @param string $table
     * @param bool   $numberOfIndices
     *
     * @return array
     */
    public function metaColumnNames($table, $numberOfIndices = false);

    /**
     * Start the mysql transaction.
     *
     * @return bool
     */
    public function startTransaction();

    /**
     * Commit the mysql transaction.
     *
     * @return bool
     */
    public function commitTransaction();

    /**
     * RollBack the mysql transaction.
     *
     * @return bool
     */
    public function rollbackTransaction();

    /**
     * Set transaction isolation level.
     * Allowed values 'READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ' and 'SERIALIZABLE'.
     *
     * @param null|string $level The level of
     *
     * @return bool
     */
    public function setTransactionIsolationLevel($level = null);

    /**
     * Calls the database UI method.
     *
     * @param integer $pollSeconds poll seconds
     */
    public function UI($pollSeconds = 5);

    /**
     * Get the last inserted ID.
     *
     * @return string The last inserted ID.
     */
    public function insert_Id();
}
