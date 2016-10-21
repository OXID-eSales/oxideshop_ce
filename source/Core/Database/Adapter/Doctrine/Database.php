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

namespace OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use OxidEsales\Eshop;
use OxidEsales\EshopCommunity\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseConnectionException;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseException;
use OxidEsales\EshopCommunity\Core\Exception\StandardException;
use PDO;

/**
 * The doctrine implementation of our database.
 *
 * @package OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine;
 */
class Database implements DatabaseInterface
{

    /**
     * Holds the necessary parameters to connect to the database
     */
    protected $connectionParameters = array();

    /**
     * @var DriverConnection The database connection.
     */
    protected $connection = null;

    /**
     * @var int The current fetch mode.
     */
    protected $fetchMode = PDO::FETCH_NUM;

    /**
     * @var array Map strings used in the shop to Doctrine constants
     */
    protected $transactionIsolationLevelMap = array(
        'READ UNCOMMITTED' => Connection::TRANSACTION_READ_UNCOMMITTED,
        'READ COMMITTED'   => Connection::TRANSACTION_READ_COMMITTED,
        'REPEATABLE READ'  => Connection::TRANSACTION_REPEATABLE_READ,
        'SERIALIZABLE'     => Connection::TRANSACTION_SERIALIZABLE
    );

    /**
     * @var array Map fetch modes used in the shop to doctrine constants
     */
    protected $fetchModeMap = array(
        DatabaseInterface::FETCH_MODE_DEFAULT => PDO::FETCH_BOTH,
        DatabaseInterface::FETCH_MODE_NUM     => PDO::FETCH_NUM,
        DatabaseInterface::FETCH_MODE_ASSOC   => PDO::FETCH_ASSOC,
        DatabaseInterface::FETCH_MODE_BOTH    => PDO::FETCH_BOTH
    );

    /**
     * The standard constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritdoc
     *
     * Each database driver needs different parameters. At the moment only the driver 'pdo_mysql' is supported.
     *
     * @param array $connectionParameters The parameters to connect to the database using the doctrine pdo_mysql driver
     */
    public function setConnectionParameters(array $connectionParameters)
    {
        if (array_key_exists('default', $connectionParameters)) {
            $this->connectionParameters = $this->getPdoMysqlConnectionParameters($connectionParameters['default']);
        }
    }

    /**
     * Connects to the database using the connection parameters set in DatabaseInterface::setConnectionParameters().
     *
     * @throws DatabaseConnectionException If a connection to the database cannot be established
     */
    public function connect()
    {
        $connection = null;

        /**
         * @todo we need a SQLLogger that logs to a (CSV?) file, as we probably do not want to log into the database.
         *
         * $configuration->setSQLLogger(new EchoSQLLogger());
         */

        try {
            $connection = $this->getConnectionFromDriverManager();
            $connection->connect();

            $this->setConnection($connection);

            $this->ensureConnectionIsEstablished($connection);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function forceMasterConnection()
    {
        if (is_null($this->connection)) {
            $this->connect();
        }
    }

    /**
     * @inheritdoc
     */
    public function forceSlaveConnection()
    {
        if (is_null($this->connection)) {
            $this->connect();
        }
    }

    /**
     * @inheritdoc
     */
    public function closeConnection()
    {
        $this->connection->close();
        gc_collect_cycles();
    }

    /**
     * Set connection
     *
     * @param Connection $connection
     */
    protected function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the connection parameters for the doctrine pdo_mysql driver
     *
     * The pdo_mysql driver accepts an array with the following keys
     *
     * user (string): Username to use when connecting to the database.
     * password (string): Password to use when connecting to the database.
     * host (string): Hostname of the database to connect to.
     * port (integer): Port of the database to connect to.
     * dbname (string): Name of the database/schema to connect to.
     * charset (string): The charset used when connecting to the database.
     * unix_socket (string): Name of the socket used to connect to the database.
     *
     * host/port and unix_socket are mutually exclusive. Currently only host/port is supported by this database adapter
     *
     * @param array $connectionParameters The parameters to connect to the database using the doctrine pdo_mysql driver
     *
     * @return array
     */
    protected function getPdoMysqlConnectionParameters(array $connectionParameters)
    {
        $pdoMysqlConnectionParameters = array(
            'driver'   => 'pdo_mysql',
            'host'     => $connectionParameters['databaseHost'],
            'dbname'   => $connectionParameters['databaseName'],
            'user'     => $connectionParameters['databaseUser'],
            'password' => $connectionParameters['databasePassword'],
            'port'     => $connectionParameters['databasePort'],
        );

        $this->addDriverOptions($pdoMysqlConnectionParameters);
        $this->addConnectionCharset(
            $pdoMysqlConnectionParameters,
            $connectionParameters['connectionCharset']
        );

        return $pdoMysqlConnectionParameters;
    }

    /**
     * Adds the param driverOptions to an existing array of connection parameters
     *
     * @param array $existingParameters
     *
     */
    protected function addDriverOptions(array &$existingParameters)
    {
        $existingParameters['driverOptions'] = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => $this->getMySqlInitCommand()
        );
    }

    /**
     * This function can be extended to add own init command for the database connection
     *
     * @return string
     */
    protected function getMySqlInitCommand()
    {
        return "SET @@SESSION.sql_mode=''";
    }

    /**
     * Determine the charset to be used when connecting to the database.
     *
     * Please be aware that different database drivers may need different options/values for setting the charset
     * or may not support setting charset at all.
     * See http://doctrine-orm.readthedocs.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connection-details
     * for details.
     *
     * Take into account that the character set must be set either on the server level, or within the database
     * connection itself (depending on the driver) for it to affect PDO::quote().
     *
     * @param array  $existingParameters
     * @param string $connectionCharset
     *
     */
    protected function addConnectionCharset(array &$existingParameters, $connectionCharset)
    {
        $sanitizedCharset = trim(strtolower((string) $connectionCharset));

        if (!empty($sanitizedCharset)) {
            $existingParameters['charset'] = $sanitizedCharset;
        }
    }

    /**
     * Get the connection parameter array.
     *
     * @todo: Map the iDebug config.inc parameter to the doctrine settings.
     *
     * @return array The connection settings parameters.
     */
    protected function getConnectionParameters()
    {
        return $this->connectionParameters;
    }

    /**
     * @inheritdoc
     *
     * The given fetch mode as used be the DatabaseInterface Class will be mapped to the Doctrine specific fetch mode.
     *
     * When the connection is opened the fetch mode will be set to a default value as defined in
     * OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database::$fetchMode.
     *
     * @param integer $fetchMode See DatabaseInterface::FETCH_MODE_* for valid values
     */
    public function setFetchMode($fetchMode)
    {
        $this->fetchMode = $this->fetchModeMap[$fetchMode];

        $this->getConnection()->setFetchMode($this->fetchMode);
    }

    /**
     * Get the first value of the first row of the result set of a given sql SELECT or SHOW statement.
     * Returns false for any other statement.
     *
     * NOTE: Although you might pass any SELECT or SHOW statement to this method, try to limit the result of the
     * statement to one single row, as the rest of the rows is simply discarded.
     *
     * @param string $query      The sql SELECT or SHOW statement.
     * @param array  $parameters Array of parameters for the given sql statement.
     *
     * @return string|false      Returns a string for SELECT or SHOW statements and FALSE for any other statement.
     */
    public function getOne($query, $parameters = array())
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        if ($this->doesStatementProduceOutput($query)) {
            return $this->getConnection()->fetchColumn($query, $parameters);
        }

        return false;
    }

    /**
     * Get an array with the values of the first row of a given sql SELECT or SHOW statement .
     * Returns an empty array for any other statement.
     * The returned value depends on the fetch mode.
     *
     * @see DatabaseInterface::setFetchMode() for how to set the fetch mode
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
     * @param string $query      The sql select statement we want to execute.
     * @param array  $parameters Array of parameters, for the given sql statement.
     *
     * @return array The row, we selected with the given sql statement.
     */
    public function getRow($query, $parameters = array())
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        try {
            $resultSet = $this->select($query, $parameters);
            $result = $resultSet->fields;
        } catch (DatabaseException $exception) {
            /** Only log exception, do not re-throw here, as legacy code expects this behavior */
            $this->logException($exception);
            $result = array();
        } catch (PDOException $exception) {
            /** Only log exception, do not re-throw here, as legacy code expects this behavior */
            $exception = $this->convertException($exception);
            $this->logException($exception);
            $result = array();
        }

        if (false == $result) {
            $result = array();
        }

        return $result;
    }

    /**
     * Quote a string in a way, that it can be used as a identifier (i.e. table name or field name) in a sql statement.
     * You are strongly encouraged to always use quote identifiers.
     *
     * @param string $string The string to be quoted.
     *
     * @return string
     */
    public function quoteIdentifier($string)
    {
        $identifierQuoteCharacter = $this->getConnection()->getDatabasePlatform()->getIdentifierQuoteCharacter();

        if (!$identifierQuoteCharacter) {
            $identifierQuoteCharacter = '`';
        }

        $string = trim(str_replace($identifierQuoteCharacter, '', $string));

        return $this->getConnection()->quoteIdentifier($string);
    }

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
    public function quote($value)
    {
        return $this->getConnection()->quote($value);
    }

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
    public function quoteArray($array)
    {
        $result = array();

        foreach ($array as $key => $item) {
            $result[$key] = $this->quote($item);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function startTransaction()
    {
        try {
            $this->getConnection()->beginTransaction();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function commitTransaction()
    {
        try {
            $this->getConnection()->commit();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * @inheritdoc
     */
    public function rollbackTransaction()
    {
        try {
            $this->getConnection()->rollBack();
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * Set the transaction isolation level.
     * Allowed values 'READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ' and 'SERIALIZABLE'.
     *
     * NOTE: Currently the transaction isolation level is set on the database session and not globally.
     * Setting the transaction isolation level globally requires root privileges in MySQL an this application should not
     * be executed with root privileges.
     * If you need to set the transaction isolation level globally, ask your database administrator to do so,
     * This method is MySQL specific, as we use the MySQL syntax for setting the transaction isolation level.
     *
     * @see Doctrine::transactionIsolationLevelMap
     *
     * @param string $level The transaction isolation level
     *
     * @throws \InvalidArgumentException|DatabaseException     *
     *
     * @return bool|integer
     */
    public function setTransactionIsolationLevel($level)
    {
        $result = false;
        $availableLevels = array_keys($this->transactionIsolationLevelMap);

        if (!in_array(strtoupper($level), $availableLevels)) {
            throw new \InvalidArgumentException();
        }

        try {
            if (in_array(strtoupper($level), $availableLevels)) {
                $result = $this->execute('SET SESSION TRANSACTION ISOLATION LEVEL ' . $level);
            }
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

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
    public function execute($query, $parameters = array())
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);

        // END deprecated

        return $this->executeUpdate($query, $parameters);
    }

    /**
     * @inheritdoc
     *
     * Affected rows will be set to 0 by this query.
     *
     */

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
     * @param string $query      The sql select statement
     * @param array  $parameters The parameters array for the given query.
     *
     * @throws DatabaseException The exception, that can occur while executing the sql statement.
     *
     * @return \OxidEsales\EshopCommunity\Core\Database\Adapter\ResultSetInterface The result of the given query.
     */
    public function select($query, $parameters = array())
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        $result = null;

        try {
            /**
             * Be aware that Connection::executeQuery is a method specifically for READ operations only.
             * This is especially important in master-slave Connection
             */
            /** @var \Doctrine\DBAL\Driver\Statement $statement Statement is prepared and executed by executeQuery() */
            $statement = $this->getConnection()->executeQuery($query, $parameters);

            $result = new ResultSet($statement);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }


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
     * Be aware that only a few database vendors have the LIMIT clause as known from MySQL.
     * The Doctrine Query Builder should be used here.
     *
     * @param string $query      The sql select statement
     * @param int    $rowCount   Maximum number of rows to return
     * @param int    $offset     Offset of the first row to return
     * @param array  $parameters The parameters array.
     *
     * @throws DatabaseException The exception, that can occur while executing the sql statement.
     *
     * @return \OxidEsales\EshopCommunity\Core\Database\Adapter\ResultSetInterface The result of the given query.
     */
    public function selectLimit($query, $rowCount = -1, $offset = -1, $parameters = array())
    {
        /**
         * Parameter validation.
         * At the moment there will be no InvalidArgumentException thrown on non numeric values as this may break
         * too many things.
         */
        if (!is_numeric($rowCount) || !is_numeric($offset)) {
            trigger_error(
                'Parameters rowCount and offset have to be numeric in DatabaseInterface::selectLimit(). ' .
                'Please fix your code as this error may trigger an exception in future versions of OXID eShop.',
                E_USER_DEPRECATED
            );
        }

        /**
         * Cast the parameters limit and offset to integer in in order to avoid SQL injection.
         */
        $rowCount = (int) $rowCount;
        $offset = (int) $offset;
        $limitClause = '';

        if ($rowCount >= 0 && $offset >= 0) {
            $limitClause = "LIMIT $rowCount OFFSET $offset";
        }

        return $this->select($query . " $limitClause ", $parameters);
    }

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
     * @param string $query      The sql select statement
     * @param array  $parameters The parameters array.
     *
     * @throws DatabaseException
     *
     * @return array The values of the first column of a corresponding sql query.
     */
    public function getCol($query, $parameters = array())
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        $result = array();

        try {
            $rows = $this->getConnection()->fetchAll($query, $parameters);
            foreach ($rows as $row) {
                // cause there is no doctrine equivalent, we take this little detour and restructure the result
                $columnNames = array_keys($row);
                $columnName = $columnNames[0];

                $result[] = $row[$columnName];
            }
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        } catch (PDOException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

    /**
     * Executes an SQL INSERT/UPDATE/DELETE query with the given parameters and returns the number of affected.
     *
     * This method supports PDO binding types as well as DBAL mapping types.
     *
     * @param string $query      The SQL query.
     * @param array  $parameters The query parameters.
     * @param array  $types      The parameter types.
     *
     * @throws DatabaseException
     *
     * @return integer The number of affected rows.
     */
    public function executeUpdate($query, $parameters = array(), $types = array())
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        $affectedRows = 0;

        try {
            $affectedRows = $this->getConnection()->executeUpdate($query, $parameters, $types);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $affectedRows;
    }

    /**
     * Get the database connection.
     *
     * @return \Doctrine\DBAL\Connection $oConnection The database connection we want to use.
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sanitize the given parameter to be an array.
     * In v5.3.0 in many places in the code false is passed instead of an empty array.
     *
     * This methods work like this:
     * Anything that evaluates to true and is not an array will cause an exception to be thrown.
     * Anything that evaluates to false will be converted into an empty array.
     * An non empty array will be returned as such.
     *
     * @param bool|array $parameter The parameter we want to be an array.
     *
     * @throws \InvalidArgumentException
     *
     * @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
     *
     * @return array The original array or an empty array, if false was passed.
     */
    private function assureParameterIsAnArray($parameter)
    {
        /** If $parameter evaluates to true and it is not an array throw an InvalidArgumentException */
        if ($parameter && !is_array($parameter)) {
            throw new \InvalidArgumentException();
        }

        /** If $parameter evaluates to false and it is not an array convert it into an array */
        if (!is_array($parameter)) {
            $parameter = array();
        }

        return $parameter;
    }

    /**
     * Return true, if the given SQL statement is a statement that may produce any output.
     *
     * There are two kinds of SQL statements.
     * One class produces output like
     * "SELECT * FROM `countries` ORDER BY `iso_code`DESC"
     * Which returns something like:
     * +----------+----------------------------------------+
     * | iso_code | name                                   |
     * +----------+----------------------------------------+
     * | AF       | Afghanistan                            |
     * | AL       | Albania                                |
     * | AS       | American Samoa                         |
     *
     * The other class does not produce any output like
     * "UPDATE countries SET (`name` = 'United States of America') WHERE iso_code = 'US'"
     *
     * @param string $query The query we want to check.
     *
     * @return bool Return true, if the given SQL statement is a statement that may produce any output
     */
    private function doesStatementProduceOutput($query)
    {
        $allowedCommands = [
            // Data Manipulation Statements
            'SELECT',
            // Commands for Prepared Statements
            'EXECUTE',
            // Statements for Condition Handling
            'GET',
            // Database Administration Statements
            'SHOW',
            'CHECKSUM',
            // MySQL Utility Statements
            'DESCRIBE',
            'EXPLAIN',
            'HELP',
        ];
        $command = $this->getFirstCommandInStatement($query);

        return in_array($command, $allowedCommands);
    }

    /**
     * Convert a given native Doctrine exception into an OxidEsales exception.
     * Note: This method is MySQL specific, as the MySQL error codes instead of SQLSTATE are used.
     *
     * @param \Exception $exception Doctrine exception to be converted
     *
     * @return \oxException Exception converted into an instance of oxException
     */
    protected function convertException(\Exception $exception)
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();

        switch (true) {
            case $exception instanceof Exception\ConnectionException:
                // ConnectionException will be mapped to DatabaseConnectionException::class
                // no break
            case $exception instanceof ConnectionException:
                /**
                 * Doctrine does not recognise "SQLSTATE[HY000] [2003] Can't connect to MySQL server on 'mysql.example'"
                 * as a connection error, as the error code 2003 is simply not treated in
                 * Doctrine\DBAL\Driver\AbstractMySQLDriver::convertException.
                 * We fix this here.
                 */
                // ConnectionException will be mapped to DatabaseConnectionException::class
                // no break
            case is_a($exception->getPrevious(), '\Exception') && in_array($exception->getPrevious()->getCode(), ['2003']):
                $exceptionClass = DatabaseConnectionException::class;
                break;
            case $exception instanceof DBALException:
                /**
                 * Doctrine passes the message and the code of the PDO Exception, which would break backward
                 * compatibility as it uses SQLSTATE error code (string), but the shop used to the (My)SQL errors (integer)
                 * See http://php.net/manual/de/class.pdoexception.php For details and discussion.
                 * Fortunately we can access PDOException and recover the original SQL error code and message.
                 */
                $pdoException = $exception->getPrevious();
                /** @var $pdoException PDOException */
                $code = $pdoException->errorInfo[1];
                $message = $pdoException->errorInfo[2];

                $exceptionClass = DatabaseException::class;

                break;
            case $exception instanceof PDOException:
                /**
                 * The shop used to the (My)SQL errors (integer) in the error code, but $pdoException uses SQLSTATE error code (string)
                 * See http://php.net/manual/de/class.pdoexception.php For details and discussion.
                 * Fortunately we can access PDOException and recover the original SQL error code and message.
                 */
                $code = $exception->errorInfo[1];
                $message = $exception->errorInfo[2];

                $exceptionClass = 'OxidEsales\EshopCommunity\Core\Exception\DatabaseException';

                break;
            default:
                $exceptionClass = 'OxidEsales\EshopCommunity\Core\Exception\DatabaseException';
        }

        /** @var \oxException $convertedException */
        $convertedException = new $exceptionClass($message, $code, $exception);

        return $convertedException;
    }

    /**
     * Handle a given exception. The standard behavior at the moment is to throw the exception passed in the parameter.
     * A second exception handling including logging will be done by the ShopControl class.
     *
     * @param StandardException $exception
     *
     * @throws StandardException
     * @throws DatabaseConnectionException
     * @throws DatabaseException
     */
    protected function handleException(StandardException $exception)
    {
        throw $exception;
    }

    /**
     * Log a given Exception the log file using the standard eShop logging mechanism.
     * Use this function whenever a exception is caught and not re-thrown.
     *
     * @param \Exception $exception
     */
    protected function logException(\Exception $exception)
    {
        /** The exception has to be converted into an instance of oxException in order to be logged like this */
        $exception = $this->convertException($exception);
        $exception->debugOut();
    }

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
     * @param string $query      If parameters are given, the "?" in the string will be replaced by the values in the array
     * @param array  $parameters Array of parameters, for the given sql statement.
     *
     * @see DatabaseInterface::setFetchMode()
     * @see Doctrine::$fetchMode
     *
     * @throws DatabaseException
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getAll($query, $parameters = array())
    {
        $result = array();
        $statement = null;

        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        try {
            $statement = $this->getConnection()->executeQuery($query, $parameters);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        if ($this->doesStatementProduceOutput($query)) {
            $result = $statement->fetchAll();
        }

        return $result;
    }

    /**
     * Return string representing the row ID of the last row that was inserted into
     * the database.
     * Returns 0 for tables without autoincrement field.
     *
     * @return string|int Row ID
     */
    public function getLastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Get the meta information about all the columns of the given table.
     * This is kind of a poor man's schema manager, which only works for MySQL.
     *
     * @param string $table The name of the table.
     *
     * @return array Array of objects with meta information of each column.
     */
    public function metaColumns($table)
    {
        $connection = $this->getConnection();
        $databaseName = $connection->getDatabase();
        $query = "
            SELECT
              COLUMN_NAME AS `Field`,
              COLUMN_TYPE AS `Type`,
              IS_NULLABLE AS `Null`,
              COLUMN_KEY AS `Key`,
              COLUMN_DEFAULT AS `Default`,
              EXTRA AS `Extra`,
              COLUMN_COMMENT AS `Comment`,
              CHARACTER_SET_NAME AS `CharacterSet`,
              COLLATION_NAME AS `Collation`
            FROM information_schema.COLUMNS
            WHERE
              TABLE_SCHEMA = '$databaseName'
              AND
              TABLE_NAME = '$table'";
        $columns = $connection->executeQuery($query)->fetchAll();
        /** Depending on the fetch mode we may find numeric or string key in the array $rawColumns */


        $result = [];

        foreach ($columns as $column) {
            $type = $this->getMetaColumnValueByKey($column, 'Type');
            $field = $this->getMetaColumnValueByKey($column, 'Field');
            $null = $this->getMetaColumnValueByKey($column, 'Null');
            $key = $this->getMetaColumnValueByKey($column, 'Key');
            $default = $this->getMetaColumnValueByKey($column, 'Default');
            $extra = $this->getMetaColumnValueByKey($column, 'Extra');
            $comment = $this->getMetaColumnValueByKey($column, 'Comment');
            $characterSet = $this->getMetaColumnValueByKey($column, 'CharacterSet');
            $collation = $this->getMetaColumnValueByKey($column, 'Collation');


            $typeInformation = explode('(', $type);
            $typeName = trim($typeInformation[0]);

            $item = new \stdClass();
            $item->name = $field;
            $item->type = $typeName;
            $item->not_null = ('no' === strtolower($null));
            $item->primary_key = (strtolower($key) == 'pri');
            $item->auto_increment = strtolower($extra) == 'auto_increment';
            $item->binary = (false !== strpos(strtolower($type), 'blob'));
            $item->unsigned = (false !== strpos(strtolower($type), 'unsigned'));
            $item->has_default = ('' === $default || is_null($default)) ? false : true;
            if ($item->has_default) {
                $item->default_value = $default;
            }

            /**
             * These variables were set only when there was a value in the previous implementation with ADOdb Lite.
             * We do it the same way here for compatibility.
             */
            list($max_length, $scale) = $this->getColumnMaxLengthAndScale($column, $item->type);
            if (-1 !== $max_length) {
                $item->max_length = (string) $max_length;
            } else {
                $item->max_length = $max_length;
            }
            if (-1 !== $scale) {
                $item->scale = (string) $scale;
            } else {
                $item->scale = null;
            }

            /** Unset has_default and default_value for binary types */
            if ($item->binary) {
                unset($item->has_default, $item->default_value);
            }

            /** Additional properties not found in ADODB lite */
            $item->comment = $comment;
            $item->characterSet = $characterSet;
            $item->collation = $collation;

            /**
             * ADODB lite properties not implemented
             *
             * @todo: implement the enums property for SET and ENUM fields
             */
            // $item->enums

            if (array_key_exists('Field', $column)) {
                $result[$item->name] = $item;
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Return true, if the connection is marked rollbackOnly.
     *
     * Doctrine manages nested transaction the following way:
     * If any of the inner transactions is rolled back, all the outer transactions will have to be rolled back also.
     * For that reason the connection will be marked as rollbackOnly and any commitTransaction will throw an exception.
     *
     * @return bool
     */
    public function isRollbackOnly()
    {
        return $this->connection->isRollbackOnly();
    }

    /**
     * Checks whether a transaction is currently active.
     *
     * @return boolean TRUE if a transaction is currently active, FALSE otherwise.
     */
    public function isTransactionActive()
    {
        return $this->connection->isTransactionActive();
    }

    /**
     * Get the value of a meta column key.
     *
     * @param array  $column The meta column, where the value has to be fetched.
     * @param string $key    The key to fetch.
     *
     * @return mixed
     */
    protected function getMetaColumnValueByKey(array $column, $key)
    {
        if (array_key_exists('Field', $column)) {
            $keyMap = array(
                'Field'        => 'Field',
                'Type'         => 'Type',
                'Null'         => 'Null',
                'Key'          => 'Key',
                'Default'      => 'Default',
                'Extra'        => 'Extra',
                'Comment'      => 'Comment',
                'CharacterSet' => 'CharacterSet',
                'Collation'    => 'Collation',
            );
        } else {
            $keyMap = array(
                'Field'        => 0,
                'Type'         => 1,
                'Null'         => 2,
                'Key'          => 3,
                'Default'      => 4,
                'Extra'        => 5,
                'Comment'      => 6,
                'CharacterSet' => 7,
                'Collation'    => 8,
            );
        }

        $result = $column[$keyMap[$key]];

        return $result;
    }


    /**
     * Get the maximal length of a given column of a given type.
     *
     * @param array  $column       The meta column for which the may length has to be found.
     * @param string $assignedType The type of the column.
     *
     * @return int[] The maximal length and the scale (in case of DECIMAL type).
     *               Both variables are -1 in case of no value can be found.
     */
    protected function getColumnMaxLengthAndScale(array $column, $assignedType)
    {
        /** @var int $maxLength The max length of a field. For floating point type or fixed point type fields the precision of the field */
        $maxLength = -1;
        /** @var int $scale The scale of floating point type or fixed point type fields */
        $scale = -1;

        /** @var string $mySqlType E.g. "CHAR(4)" or "DECIMAL(5,2)" or "tinyint(1) unsigned" */
        $mySqlType = $this->getMetaColumnValueByKey($column, 'Type');
        /** Get the maximum display width for the type */

        /** Match Precision an scale E.g DECIMAL(5,2) */
        if (preg_match("/^(.+)\((\d+),(\d+)/", $mySqlType, $matches)) {
            if (is_numeric($matches[2])) {
                $maxLength = $matches[2];
            }
            if (is_numeric($matches[3])) {
                $scale = $matches[3];
            }
            /** Match max length E.g CHAR(4) */
        } elseif (preg_match("/^(.+)\((\d+)/", $mySqlType, $matches)) {
            if (is_numeric($matches[2])) {
                $maxLength = $matches[2];
            }
            /**
             * Match List type E.g. SET('A', 'B', 'CDE)
             * In this case the length will be the string length of the longest element
             */
        } elseif (preg_match("/^(enum|set)\((.*)\)$/i", strtolower($mySqlType), $matches)) {
            if ($matches[2]) {
                $pieces = explode(",", $matches[2]);
                /** The array values contain 2 quotes, so we have to subtract 2 from the strlen */
                $maxLength = max(array_map("strlen", $pieces)) - 2;
                if ($maxLength <= 0) {
                    $maxLength = 1;
                }
            }
        }

        /** Numeric types, which may have a maximum length */
        $integerTypes = array('INTEGER', 'INT', 'SMALLINT', 'TINYINT', 'MEDIUMINT', 'BIGINT');
        $fixedPointTypes = array('DECIMAL', 'NUMERIC');
        $floatingPointTypes = array('FLOAT', 'DOUBLE');

        /** Text types, which may have a maximum length */
        $textTypes = array('CHAR', 'VARCHAR');

        /** Date types, which may have a maximum length */
        $dateTypes = array('YEAR');

        $assignedType = strtoupper($assignedType);
        if ((
                in_array($assignedType, $integerTypes) ||
                in_array($assignedType, $fixedPointTypes) ||
                in_array($assignedType, $floatingPointTypes) ||
                in_array($assignedType, $textTypes) ||
                in_array($assignedType, $dateTypes)
            ) && -1 == $maxLength
        ) {
            /**
             * @todo: If the assigned type is one of the following and maxLength is -1, then, if applicable the default max length ot that type should be assigned.
             */
        }

        return array((int) $maxLength, (int) $scale);
    }

    /**
     * This method strips SQL comments and whitespaces to find the first effective SQL command in a giver statement
     *
     * @param string $query The query to extract the command from
     *
     * @return string The first effective SQL command
     */
    protected function getFirstCommandInStatement($query)
    {
        $sqlComments = '@(([\'"]).*?[^\\\]\2)|((?:\#|--).*?$|/\*(?:[^/*]|/(?!\*)|\*(?!/)|(?R))*\*\/)\s*|(?<=;)\s+@ms';
        $uncommentedQuery = preg_replace($sqlComments, '$1', $query);

        $command = strtoupper(
            trim(
                explode(' ', trim($uncommentedQuery))[0]
            )
        );

        return $command;
    }

    /**
     * Ensure, that the given connection is established successful.
     *
     * @param \Doctrine\DBAL\Connection $connection The connection we want to ensure, if it is established.
     *
     * @throws \Exception If we are not connected correctly to the database.
     */
    protected function ensureConnectionIsEstablished($connection)
    {
        if (!$this->isConnectionEstablished($connection)) {
            $message = $this->createConnectionErrorMessage($connection);

            throw new ConnectionException($message);
        }
    }

    /**
     * Determine, if the connection is established successful.
     *
     * @param \Doctrine\DBAL\Connection $connection The connection we want to know, if it is established.
     *
     * @return bool Is the connection successfully established?
     */
    protected function isConnectionEstablished($connection)
    {
        return $connection->isConnected();
    }

    /**
     * Get the connection from the Doctrine DBAL DriverManager.
     *
     * @throws DBALException
     *
     * @return Connection The connection to the database.
     */
    protected function getConnectionFromDriverManager()
    {
        $configuration = new Configuration();
        $connectionParameters = $this->getConnectionParameters();

        return DriverManager::getConnection($connectionParameters, $configuration);
    }

    /**
     * Create the message we want to throw, if there was a connection error.
     *
     * @param Connection $connection The connection.
     *
     * @return string The message we want throw if there was a connection error.
     */
    protected function createConnectionErrorMessage($connection)
    {
        $message =
            'Not connected to database. dsn: ' .
            $connection->getDriver()->getName() .
            '://' .
            '****:****@' .
            $connection->getHost() . ':' . $connection->getPort() .
            '/' . $connection->getDatabase();

        return $message;
    }
}
