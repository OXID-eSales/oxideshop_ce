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

namespace OxidEsales\Eshop\Core\Database\Adapter;

use OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseException;

/**
 * The database connection interface specifies how a database connection should look and act.
 */
interface DatabaseInterface
{

    /**
     * The default fetch mode as implemented by the database driver, in Doctrine this is usually FETCH_MODE_BOTH
     *
     * @deprecated since 6.0 (2016-04-19); This constant is confusing as the shop uses a different default fetch mode.
     */
    const FETCH_MODE_DEFAULT = 0;

    /**
     * Fetch the query result into an array with integer keys.
     * This is the default fetch mode as it is set by OXID eShop on opening a database connection.
     */
    const FETCH_MODE_NUM = 1;

    /** Fetch the query result into an array with string keys */
    const FETCH_MODE_ASSOC = 2;

    /** Fetch the query result into a mixed array with both integer and string keys */
    const FETCH_MODE_BOTH = 3;

    /**
     * Set the necessary connection parameters to connect to the database.
     * The parameter array must at least contain the key 'default'. E.g.
     * [
     *  'default' => [
     *      'databaseDriver'    => '', // string At the moment only 'pdo_mysql' is supported
     *      'databaseHost'      => '', // string
     *      'databasePort'      => '', // integer Optional, defaults to port 3306
     *      'databaseName'      => '', // string
     *      'databaseUser'      => '', // string
     *      'databasePassword'  => '', // string
     *      'connectionCharset' => '', // string Optional, defaults to the servers connection character set
     *      ]
     * ]
     *
     * @param array $connectionParameters
     */
    public function setConnectionParameters(array $connectionParameters);

    /**
     * Connects to the database using the connection parameters set in DatabaseInterface::setConnectionParameters().
     *
     * @throws DatabaseConnectionException If a connection to the database cannot be established
     */
    public function connect();

    /**
     * Force database master connection.
     *
     * @return null
     */
    public function forceMasterConnection();

    /**
     * Force database slave connection. Do not use this function unless
     * you know exactly what you are doing. Usage of this function
     * can lead to write access to a MySQL slave and getting replication out
     * of sync.
     *
     * @return null
     */
    public function forceSlaveConnection();

    /**
     * Closes an open connection
     *
     * @return null
     */
    public function closeConnection();

    /**
     * Set the fetch mode of an open database connection.
     *
     * After the connection has been opened, this method may be used to set the fetch mode to any of the valid fetch
     * modes as defined in DatabaseInterface::FETCH_MODE_*
     *
     * NOTE: This implies, that it is not safe to make any assumptions about the current fetch mode of the connection.
     *
     * @param int $fetchMode See DatabaseInterface::FETCH_MODE_* for valid values
     */
    public function setFetchMode($fetchMode);

    /**
     * Get the first value of the first row of the result set of a given sql SELECT or SHOW statement.
     * Returns false for any other statement.
     *
     * NOTE: Although you might pass any SELECT or SHOW statement to this method, try to limit the result of the
     * statement to one single row, as the rest of the rows is simply discarded.
     *
     * @param string $query          The sql SELECT or SHOW statement.
     * @param array  $parameters     Array of parameters for the given sql statement.
     *
     * @return string|false          Returns a string for SELECT or SHOW statements and FALSE for any other statement.
     */
    public function getOne($query, $parameters = array());

    /**
     * Get an array with the values of the first row of a given sql SELECT or SHOW statement .
     * Returns an empty array for any other statement.
     * The returned value depends on the fetch mode.
     *  @see DatabaseInterface::setFetchMode() for how to set the fetch mode
     *
     * The keys of the array may be numeric, strings or both, depending on the FETCH_MODE_* of the connection.
     * Set the desired fetch mode with DatabaseInterface::setFetchMode() before calling this method.
     *
     * NOTE: Although you might pass any SELECT or SHOW statement to this method, try to limit the result of the
     * statement to one single row, as the rest of the rows is simply discarded.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $result = Database::getDb->getOne(
     *   'SELECT ´id´ FROM ´mytable´ WHERE ´id´ = ? LIMIT 0, 1',
     *   array($id1)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     *
     * @param string $sqlSelect      The sql select statement we want to execute.
     * @param array  $parameters     Array of parameters, for the given sql statement.
     *
     * @return array The row, we selected with the given sql statement.
     */
    public function getRow($sqlSelect, $parameters = array());

    /**
     * Return the first column of all rows of the results of a given sql SELECT or SHOW statement as an numeric array.
     * Throws an exception for any other statement.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $result = Database::getDb->getRow(
     *   'SELECT * FROM ´mytable´ WHERE ´id´ = ? LIMIT 0, 1',
     *   array($id1)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $sqlSelect      The sql select statement
     * @param array  $parameters     The parameters array.
     *
     * @throws DatabaseException
     *
     * @return array The values of the first column of a corresponding sql query.
     */
    public function getCol($sqlSelect, $parameters = array());

    /**
     * Get an multi-dimensional array of arrays with the values of the all rows of a given sql SELECT or SHOW statement.
     * Returns an empty array for any other statement.
     *
     * The keys of the first level array are numeric.
     * The keys of the second level arrays may be numeric, strings or both, depending on the FETCH_MODE_* of the connection.
     * Set the desired fetch mode with DatabaseInterface::setFetchMode() before calling this method.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $result = Database::getDb->getAll(
     *   'SELECT * FROM ´mytable´ WHERE ´id´ = ? OR ´id´ = ? LIMIT 0, 1',
     *   array($id1, $id2)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $query          If parameters are given, the "?" in the string will be replaced by the values in the array
     * @param array  $parameters     Array of parameters, for the given sql statement.
     *
     * @see DatabaseInterface::setFetchMode()
     * @see Doctrine::$fetchMode
     *
     * @throws     DatabaseException
     * @throws     \InvalidArgumentException
     *
     * @return array
     */
    public function getAll($query, $parameters = array());

    /**
     * Return the results of a given sql SELECT or SHOW statement as a ResultSet.
     * Throws an exception for any other statement.
     *
     * The values of first row of the result may be via resultSet's fields property.
     * This property is an array, which keys may be numeric, strings or both, depending on the FETCH_MODE_* of the connection.
     * All further rows can be accessed via the specific methods of ResultSet.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $resultSet = Database::getDb->select(
     *   'SELECT * FROM ´mytable´ WHERE ´id´ = ? OR ´id´ = ?',
     *   array($id1, $id2)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $sqlSelect      The sql select statement
     * @param array  $parameters     The parameters array for the given query.
     *
     * @throws DatabaseException The exception, that can occur while executing the sql statement.
     *
     * @return ResultSetInterface The result of the given query.
     */
    public function select($sqlSelect, $parameters = array());

    /**
     * Return the results of a given sql SELECT or SHOW statement limited by a LIMIT clause as a ResultSet.
     * Throws an exception for any other statement.
     *
     * The values of first row of the result may be via resultSet's fields property.
     * This property is an array, which keys may be numeric, strings or both, depending on the FETCH_MODE_* of the connection.
     * All further rows can be accessed via the specific methods of ResultSet.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $resultSet = Database::getDb->selectLimit(
     *   'SELECT * FROM ´mytable´ WHERE ´id´ = ? OR ´id´ = ?',
     *   $rowCount,
     *   $offset,
     *   array($id1, $id2)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $sqlSelect  The sql select statement
     * @param int    $rowCount   Maximum number of rows to return
     * @param int    $offset     Offset of the first row to return
     * @param array  $parameters The parameters array.
     *
     * @throws DatabaseException The exception, that can occur while executing the sql statement.
     *
     * @return ResultSetInterface The result of the given query.
     */
    public function selectLimit($sqlSelect, $rowCount = -1, $offset = -1, $parameters = array());
    /**
     * Execute read statements like SELECT or SHOW and return the results as a ResultSet.
     * Execute non read statements like INSERT, UPDATE, DELETE and return the number of rows affected by the statement.
     *
     * IMPORTANT:
     * You are strongly encouraged to use prepared statements like this:
     * $resultSet = Database::getDb->execute(
     *   'SELECT * FROM ´mytable´ WHERE ´id´ = ? OR ´id´ = ?',
     *   array($id1, $id2)
     * );
     * If you will not use prepared statements, you MUST quote variables the values with quote(), otherwise you create a
     * SQL injection vulnerability.
     *
     * @param string $query      The sql statement we want to execute.
     * @param array  $parameters The parameters array.
     *
     * @throws DatabaseException
     *
     * @return integer Number of rows affected by the SQL statement
     */
    public function execute($query, $parameters = array());

    /**
     * Quote a string or a numeric value in a way, that it might be used as a value in a sql statement.
     * Returns false for values that cannot be quoted.
     *
     * NOTE: It is not safe to use the return value of this function in a query. There will be no risk of SQL injection,
     * but when the statement is executed and the value could not have been quoted, a DatabaseException is thrown.
     * You are strongly encouraged to always use prepared statements instead of quoting the values on your own.
     * E.g. use
     * $resultSet = Database::getDb->select(
     *   'SELECT * FROM ´mytable´ WHERE ´id´ = ? OR ´id´ = ?',
     *   array($id1, $id2)
     * );
     * instead of
     * $resultSet = Database::getDb->select(
     *  'SELECT * FROM ´mytable´ WHERE ´id´ = ' . Database::getDb->quote($id1) . ' OR ´id´ = ' . Database::getDb->quote($id1)
     * );
     *
     * @param mixed $value The string or numeric value to be quoted.
     *
     * @return false|string The given string or numeric value converted to a string surrounded by single quotes or set to false, if the value could not have been quoted.
     */
    public function quote($value);

    /**
     * Quote every value in a given array in a way, that it might be used as a value in a sql statement and return the
     * result as a new array. Numeric values will be converted to strings which quotes.
     * The keys and their order of the returned array will be the same as of the input array.
     *
     * NOTE: It is not safe to use the return value of this function in a query. There will be no risk of SQL injection,
     * but when the statement is executed and the value could not have been quoted, a DatabaseException is thrown.
     * You are strongly encouraged to always use prepared statements instead of quoting the values on your own.
     *
     * @param array $array The strings to quote as an array.
     *
     * @return array Array with all string and numeric values quoted with single quotes or set to false, if the value could not have been quoted.
     */
    public function quoteArray($array);

    /**
     * Quote a string in a way, that it can be used as a identifier (i.e. table name or field name) in a sql statement.
     * You are strongly encouraged to always use quote identifiers.
     *
     * @param string $string The string to be quoted.
     *
     * @return string
     */
    public function quoteIdentifier($string);

    /**
     * Get the meta information about all the columns of the given table.
     * This is kind of a poor man's schema manager, which only works for MySQL.
     *
     * @param string $table The name of the table.
     *
     * @return array Array of objects with meta information of each column.
     */
    public function metaColumns($table);

    /**
     * Start a database transaction.
     *
     * @throws DatabaseException
     */
    public function startTransaction();

    /**
     * Commit a database transaction.
     *
     * @throws DatabaseException
     */
    public function commitTransaction();

    /**
     * RollBack a database transaction.
     *
     * @throws DatabaseException
     */
    public function rollbackTransaction();

    /**
     * @inheritdoc
     *
     * Note: This method is MySQL specific, as we use the MySQL syntax for setting the transaction isolation level.
     *
     * @see Doctrine::transactionIsolationLevelMap
     *
     * @return bool|integer
     */

    /**
     * Set the transaction isolation level.
     * Allowed values 'READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ' and 'SERIALIZABLE'.
     *
     * NOTE: Currently the transaction isolation level is set on the database session and not globally.
     * Setting the transaction isolation level globally requires root privileges in MySQL an this application should not
     * be executed with root privileges.
     * If you need to set the transaction isolation level globally, ask your database administrator to do so,
     *
     * @param string $level The transaction isolation level
     *
     * @throws \InvalidArgumentException|DatabaseException
     */
    public function setTransactionIsolationLevel($level);

    /**
     * Return true, if the connection is marked rollbackOnly.
     *
     * @return bool
     */
    public function isRollbackOnly();

    /**
     * Checks whether a transaction is currently active.
     *
     * @return boolean TRUE if a transaction is currently active, FALSE otherwise.
     */
    public function isTransactionActive();

    /**
     * Return string representing the row ID of the last row that was inserted into
     * the database.
     * Returns 0 for tables without autoincrement field.
     *
     * @return string|int Row ID
     */
    public function getLastInsertId();
}
