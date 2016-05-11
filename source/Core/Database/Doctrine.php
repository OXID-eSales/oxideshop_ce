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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use OxidEsales\Eshop;
use OxidEsales\Eshop\Core\Database\Adapter\DoctrineEmptyResultSet;
use OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet;
use OxidEsales\Eshop\Core\exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\exception\DatabaseException;

/**
 * The doctrine implementation of our database.
 *
 * @package OxidEsales\Eshop\Core\Database
 */
class Doctrine implements DatabaseInterface
{

    /**
     * Holds the necessary parameters to connect to the database
     */
    protected $connectionParameters = array();
    /**
     * @var \Doctrine\DBAL\Connection The database connection.
     */
    protected $connection = null;

    /**
     * @var int The number of rows affected by the last sql query.
     */
    protected $affectedRows = 0;

    /**
     * @var int The current fetch mode.
     */
    protected $fetchMode = \PDO::FETCH_NUM;

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
        DatabaseInterface::FETCH_MODE_DEFAULT => \PDO::FETCH_BOTH,
        DatabaseInterface::FETCH_MODE_NUM     => \PDO::FETCH_NUM,
        DatabaseInterface::FETCH_MODE_ASSOC   => \PDO::FETCH_ASSOC,
        DatabaseInterface::FETCH_MODE_BOTH    => \PDO::FETCH_BOTH
    );

    /**
     * The standard constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Set the connection parameters to connect to the database.
     * Each database driver needs different parameters. At the moment only the driver 'pdo_mysql' is supported.
     *
     * @param array $connectionParameters The parameters to connect to the database using the doctrine pdo_mysql driver
     */
    public function setConnectionParameters(array $connectionParameters)
    {
        if (array_key_exists('master', $connectionParameters) && array_key_exists('slaves', $connectionParameters)) {
            $this->setConnectionParametersForMasterSlave($connectionParameters);
        } else {
            /** @var $connectionParameters */
            $connectionParameters = $this->getPdoMysqlConnectionParameters($connectionParameters);

            $this->connectionParameters = $connectionParameters;
        }
    }

    /**
     * @throws DatabaseConnectionException
     */
    public function connect()
    {
        $connection = null;

        $connectionParameters = $this->getConnectionParameters();
        
        $configuration = new Configuration();
        /**
         * TODO we need a SQLLogger that logs to a (CSV?) file, as we probably do not want to log into the database.
         *
         * $configuration->setSQLLogger(new EchoSQLLogger());
         */

        try {
            $connection = DriverManager::getConnection($connectionParameters, $configuration);
            $connection->connect();
            if (! $connection->isConnected()) {
                $dsn = $connection->getDriver()->getName() .
                       '://' .
                       '****:****@' .
                       $connection->getHost() . ':' .  $connection->getPort() .
                       '/' . $connection->getDatabase();
                throw new DBALException('Not connected to database. dsn: ' . $dsn);
            }
            $this->setConnection($connection);
        } catch (DBALException $exception) {
            $exception = new DatabaseConnectionException($exception->getMessage(), $exception->getCode(), $exception);
            $this->handleException($exception);
        }
    }

    /**
     * Set connection
     *
     * @param Connection $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * TODO to be implemented
     *
     * @param array $connectionParameters
     */
    protected function setConnectionParametersForMasterSlave(array $connectionParameters)
    {
        $wrapperClass = array('wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection');

        $this->connectionParameters = array_merge($connectionParameters, $wrapperClass);
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
         */
        /**
         * @var array Map charset as passed by the caller to doctrine charsets
         */
        $charsetMap = array(
            'utf-8'  => 'utf8',
            'utf8'   => 'utf8',
            'latin1' => 'latin1'
        );
        $sanitizedCharset = trim(strtolower($connectionParameters['connectionCharset']));
        $pdoCharset = $charsetMap[$sanitizedCharset];

        $connectionParameters = array(
            'driver'   => 'pdo_mysql',
            'host'     => $connectionParameters['databaseHost'],
            'dbname'   => $connectionParameters['databaseName'],
            'user'     => $connectionParameters['databaseUser'],
            'password' => $connectionParameters['databasePassword'],
            'port'     => $connectionParameters['databasePort'],
            'charset'  => $pdoCharset,
        );
        
        return $connectionParameters;
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
     * Set the fetch mode of an open database connection.
     * The given fetch mode as used be the DatabaseInterface Class will be mapped to the Doctrine specific fetch mode.
     *
     * When the connection is opened the fetch mode will be set to a default value as defined in Doctrine::$fetchMode.
     *
     * Once the connection has been opened, the fetch mode might be set to any of the valid fetch modes as defined in
     * DatabaseInterface::FETCH_MODE_*
     * This implies that piece a of code should make no assumptions about the current fetch mode of the connection,
     * but rather set it explicitly, before retrieving the results.
     *
     * @param integer $fetchMode See DatabaseInterface::FETCH_MODE_* for valid values
     */
    public function setFetchMode($fetchMode)
    {
        $this->fetchMode = $this->fetchModeMap[$fetchMode];

        $this->getConnection()->setFetchMode($this->fetchMode);
    }

    /**
     * Get one column, which you have to give into the sql select statement, of the first row, corresponding to the
     * given sql statement.
     *
     * @param string $sqlSelect      The sql select statement
     * @param array  $parameters     Array of parameters, for the given sql statement.
     * @param bool   $executeOnSlave Should the given sql statement executed on the slave?
     *
     * @return string The first column of the first row, which is fitting to the given sql select statement.
     */
    public function getOne($sqlSelect, $parameters = array(), $executeOnSlave = true)
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        if ($this->isSelectStatement($sqlSelect)) {
            return $this->getConnection()->fetchColumn($sqlSelect, $parameters);
        }

        return false;
    }

    /**
     * Get one row of the corresponding sql select statement.
     * The returned value depends on the fetch mode.
     *
     * @see DatabaseInterface::setFetchMode() for how to set the fetch mode
     * @see Doctrine::$fetchMode for the default fetch mode
     *
     * @param string $sqlSelect      The sql select statement we want to execute.
     * @param array  $parameters     Array of parameters, for the given sql statement.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @return array The row, we selected with the given sql statement.
     */
    public function getRow($sqlSelect, $parameters = array(), $executeOnSlave = true)
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        try {
            $resultSet = $this->select($sqlSelect, $parameters, $executeOnSlave);
            $result = $resultSet->fetchRow();
        } catch (DatabaseException $exception) {
            $this->logException($exception);
            $result = array();
        } catch (PDOException $exception) {
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
     * Get the number of rows, which where changed during the last sql statement.
     *
     * @return int The number of rows affected by the sql statement.
     */
    public function affectedRows()
    {
        return $this->getAffectedRows();
    }

    /**
     * Quote the given string. Same as qstr.
     *
     * @param string $value The string we want to quote.
     *
     * @return string The given string in quotes.
     */
    public function quote($value)
    {
        return $this->getConnection()->quote($value);
    }

    /**
     * Quote every string in the given array.
     *
     * @param array $arrayOfStrings The strings to quote as an array.
     *
     * @return array The given strings quoted.
     */
    public function quoteArray($arrayOfStrings)
    {
        $result = array();

        foreach ($arrayOfStrings as $key => $item) {
            $result[$key] = $this->quote($item);
        }

        return $result;
    }

    /**
     * Start a mysql transaction.
     *
     * @throws DatabaseException
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
     * Commit a mysql transaction.
     *
     * @throws DatabaseException
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
     * Rollback a mysql transaction.
     *
     * @throws DatabaseException
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
     *
     * Note: This method is MySQL specific, as we use the MySQL syntax for setting the transaction isolation level. 
     * 
     * Allowed values are 'READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ', 'SERIALIZABLE'.
     *
     * @param string $level The level of transaction isolation we want to set.
     *
     * @see Doctrine::transactionIsolationLevelMap
     *
     * @throws \InvalidArgumentException|DatabaseException
     *
     * @return bool|DoctrineEmptyResultSet|DoctrineResultSet
     */
    public function setTransactionIsolationLevel($level)
    {
        $availableLevels = array_keys($this->transactionIsolationLevelMap);

        if (!in_array(strtoupper($level), $availableLevels)) {
            throw new \InvalidArgumentException();
        }

        try {
            $result = false;

            if (in_array(strtoupper($level), $availableLevels)) {
                $result = $this->execute('SET SESSION TRANSACTION ISOLATION LEVEL ' . $level);
            }

            return $result;
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }
    }

    /**
     * Execute the given query and return the corresponding result set.
     *
     * @param string $query      The query we want to execute.
     * @param array  $parameters The parameters for the given query.
     *
     * @throws DatabaseException
     *
     * @return DoctrineEmptyResultSet|DoctrineResultSet
     */
    public function execute($query, $parameters = array())
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        /**
         * We divide the execution here, cause it is easier to achieve the ADOdb Lite behavior this way:
         * ADOdb Lite returns different kinds of result sets:
         * - DoctrineResultSet for "SELECT"
         * - DoctrineEmptyResultSet for the rest of queries
         */
        if ($this->isSelectStatement($query)) {
            /** @var DoctrineResultSet $result */
            $result = $this->select($query, $parameters);
        } else {
            /** @var DoctrineEmptyResultSet $result */
            $result = $this->executeUpdate($query, $parameters);
        }

        return $result;
    }

    /**
     * Run a given select sql statement on the database.
     * Affected rows will be set to 0 by this query.
     *
     * @param string $sqlSelect      The sql select statement we want to execute.
     * @param array  $parameters     The parameters for the given query.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @throws DatabaseException The exception, that can occur while running the sql statement.
     *
     * @return DoctrineResultSet The result of the given query.
     */
    public function select($sqlSelect, $parameters = array(), $executeOnSlave = true)
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
            $statement = $this->getConnection()->executeQuery($sqlSelect, $parameters);
            $result = new DoctrineResultSet($statement);

            $this->setAffectedRows($result->recordCount());
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
     * Run a given select sql statement with a limit clause.
     * Be aware that only a few database vendors have the LIMIT clause as known from MySQL.
     * The Doctrine Query Builder should be used here.
     *
     * @param string     $sqlSelect      The sql select statement we want to execute.
     * @param int        $rowCount       Maximum number of rows to return
     * @param int        $offset         Offset of the first row to return
     * @param array|bool $parameters     The parameters array.
     * @param bool       $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @throws DatabaseException
     *
     * @return DoctrineResultSet The result of the given query.
     */
    public function selectLimit($sqlSelect, $rowCount = -1, $offset = -1, $parameters = false, $executeOnSlave = true)
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

        return $this->select($sqlSelect . " $limitClause ", $parameters, $executeOnSlave);
    }

    /**
     * Get the values of a column.
     *
     * @param string $sqlSelect      The sql select statement we want to execute.
     * @param array  $parameters     The parameters array.
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @todo: What kind of array do we expect numeric or assoc? Does it depends on FETCH_MODE?
     *
     * @return array The values of a column of a corresponding sql query.
     */
    public function getCol($sqlSelect, $parameters = array(), $executeOnSlave = true)
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        $rows = $this->getConnection()->fetchAll($sqlSelect, $parameters);

        $result = array();
        foreach ($rows as $row) {
            // cause there is no doctrine equivalent, we take this little detour and restructure the result
            $columnNames = array_keys($row);
            $columnName = $columnNames[0];

            $result[] = $row[$columnName];
        }

        return $result;
    }

    /**
     * Closes an open connection
     */
    public function closeConnection()
    {
        $this->connection->close();
    }

    /**
     * Executes an SQL INSERT/UPDATE/DELETE query with the given parameters, sets the number of affected rows and returns
     * an empty DoctrineResultSet.
     *
     * This method supports PDO binding types as well as DBAL mapping types.
     *
     * @param string $query      The SQL query.
     * @param array  $parameters The query parameters.
     * @param array  $types      The parameter types.
     *
     * @throws DatabaseException
     *
     * @return DoctrineEmptyResultSet
     */
    protected function executeUpdate($query, $parameters = array(), $types = array())
    {
        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        try {
            $affectedRows = $this->getConnection()->executeUpdate($query, $parameters, $types);
            $this->setAffectedRows($affectedRows);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        $result = new DoctrineEmptyResultSet();

        return $result;
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
     * Set the number of the rows, changed by the last query.
     *
     * @param int $affectedRows How many rows did the last query changed?
     */
    protected function setAffectedRows($affectedRows)
    {
        $this->affectedRows = $affectedRows;
    }

    /**
     * Get the number of the rows, changed by the last query.
     *
     * @return int How many rows did the last query changed?
     */
    protected function getAffectedRows()
    {
        return $this->affectedRows;
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
     * Check, if the given sql query is a select statement. We handle 'show' as a select statement.
     *
     * @todo It is not safe to have comments at the beginning of the query string.
     *
     * @param string $query The query we want to check.
     *
     * @return bool Is the given query a select statement?
     */
    private function isSelectStatement($query)
    {
        $formedQuery = strtoupper(trim($query));

        return (0 === strpos($formedQuery, 'SELECT') || 0 === strpos($formedQuery, 'SHOW'));
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
                $exceptionClass = 'oxConnectionException';
                break;
            case $exception instanceof DBALException:
                /**
                 * Doctrine passes the message and the code of the PDO Exception, which would break backward
                 * compatibility as it uses SQLSTATE error code (string), but the shop used to the (My)SQL errors (integer)
                 * See http://php.net/manual/de/class.pdoexception.php For details and discussion.
                 * Fortunately we can access PDOException and recover the original SQL error code and message.
                 */
                /** @var PDOException  $pdoException*/
                $pdoException = $exception->getPrevious();
                $code = $pdoException->errorInfo[1];
                $message = $pdoException->errorInfo[2];
                $exceptionClass = 'OxidEsales\Eshop\Core\exception\DatabaseException';
                break;
            case $exception instanceof PDOException:
                /**
                 * The shop used to the (My)SQL errors (integer) in the error code, but $pdoException uses SQLSTATE error code (string)
                 * See http://php.net/manual/de/class.pdoexception.php For details and discussion.
                 * Fortunately we can access PDOException and recover the original SQL error code and message.
                 */
                $code = $exception->errorInfo[1];
                $message = $exception->errorInfo[2];
                $exceptionClass = 'OxidEsales\Eshop\Core\exception\DatabaseException';
                break;
            default:
                $exceptionClass = 'OxidEsales\Eshop\Core\exception\DatabaseException';
        }

        /** @var \oxException $convertedException */
        $convertedException = new $exceptionClass($message, $code, $exception);

        return $convertedException;
    }

    /**
     * Handle a given exception. The standard behavior at the moment is to throw the exception passed in the parameter.
     * A second exception handling including logging will be done by the ShopControl class.
     *
     * @param \oxException $exception
     *
     * @throws \Exception|\oxConnectionException|DatabaseException
     */
    protected function handleException(\oxException $exception)
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
     * Get all values as an array.
     * The format of returned the array depends on the fetch mode.
     * Set the desired fetch mode with DatabaseInterface::setFetchMode() before calling this method.
     * The default fetch mode is defined in Doctrine::$fetchMode
     *
     * @param string $sqlSelect      The sql select statement we want to execute.
     * @param array  $parameters     must loosely evaluate to false or must be an array
     * @param bool   $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @see DatabaseInterface::setFetchMode()
     * @see Doctrine::$fetchMode
     *
     * @throws     DatabaseException
     * @throws     \InvalidArgumentException
     *
     * @return array
     */
    public function getAll($sqlSelect, $parameters = array(), $executeOnSlave = true)
    {
        $statement = null;

        // @deprecated since v6.0 (2016-04-13); Backward compatibility for v5.3.0.
        $parameters = $this->assureParameterIsAnArray($parameters);
        // END deprecated

        try {
            $statement = $this->getConnection()->executeQuery($sqlSelect, $parameters);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        $result = $statement->fetchAll();

        return $result;
    }

    /**
     * Get the last inserted ID.
     *
     * @return string The last inserted ID.
     */
    public function lastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * Get the meta information about all the columns of the given table.
     *
     * @param string $table Table name.
     *
     * @return array Array of objects with meta information of each column.
     */
    public function metaColumns($table)
    {
        $columns = $this->getConnection()->executeQuery('SHOW COLUMNS FROM ' . $table)->fetchAll();

        $result = [];

        foreach ($columns as $column) {
            $typeInformation = explode('(', $column[1]);
            $typeName = $typeInformation[0];
            $typeLength = explode(')', $typeInformation[1])[0];

            /**
             * We are skipping the doctrine unsupported features AND the hard to fetch information here.
             */

            $item = new \stdClass();
            $item->name = $column[0];
            $item->type = $typeName;
            $item->max_length = $typeLength;
            $item->not_null = ('NO' === $column[2]) ? true : false;
            // $item->primary_key = '';
            // $item->auto_increment = '';
            // $item->binary = '';
            // $item->unsigned = '';
            // $item->has_default = '';
            // $item->scale = '';

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Calls the database UI method.
     *
     * @param integer $pollSeconds poll seconds
     */
    public function UI($pollSeconds = 5)
    {
        // TODO to be implemented or deprecated in DatabaseInterface
    }
}

