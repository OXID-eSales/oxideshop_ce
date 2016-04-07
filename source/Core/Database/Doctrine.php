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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use OxidEsales\Eshop;
use OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet;
use OxidEsales\Eshop\Core\exception\DatabaseException;
use oxLegacyDb;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The doctrine implementation of our database.
 *
 * @package OxidEsales\Eshop\Core\Database
 */
class Doctrine extends oxLegacyDb implements DatabaseInterface, LoggerAwareInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

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
        $this->setConnection($this->createConnection());

        /** Set the logger to the NullLogger until setLogger is called */
        $logger = new NullLogger();
        $this->setLogger($logger);
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the database connection.
     *
     * @param \Doctrine\DBAL\Connection $oConnection The database connection we want to use.
     */
    public function setConnection($oConnection)
    {
        $this->connection = $oConnection;
    }

    /**
     * Set the fetch mode.
     * Return the previous fetch mode.
     * The given fetch mode as used be the DatabaseInterface Class will be mapped and and stored as the mode used by
     * Doctrine.
     * The returned fetch mode is the re-mapped fetch mode as used by the DatabaseInterface class
     *
     * @param int $fetchMode See DatabaseInterface::FETCH_MODE_* for valid values
     *
     * @see DatabaseInterface::FETCH_MODE_* constants
     *
     * @return int The previous fetch mode as DatabaseInterface::FETCH_MODE_* constants
     */
    public function setFetchMode($fetchMode)
    {
        $flippedFetchModeMap = array_flip($this->fetchModeMap);
        $previousFetchMode = $flippedFetchModeMap[$this->fetchMode];

        $this->fetchMode = $this->fetchModeMap[$fetchMode];

        $this->getConnection()->setFetchMode($this->fetchMode);

        return $previousFetchMode;
    }

    /**
     * Get the number of rows, which where changed during the last sql statement.
     *
     * @return int The number of rows affected by the sql statement.
     */
    public function affected_rows()
    {
        return $this->getAffectedRows();
    }

    /**
     * Get the last error number, occurred while executing a sql statement through any of the methods in this class.
     *
     * @return int The last mysql error number.
     */
    public function errorNo()
    {
        $errorInformation = $this->getConnection()->errorInfo();

        $errorNumber = 0;

        if (is_array($errorInformation) && array_key_exists('1', $errorInformation) && !is_null($errorInformation[1])) {
            $errorNumber = $errorInformation[1];
        }

        return $errorNumber;
    }

    /**
     * Get the last error message, occurred while executing a sql statement through any of the methods in this class.
     *
     * @return string The last error message.
     */
    public function errorMsg()
    {
        $errorInformation = $this->getConnection()->errorInfo();

        $errorMessage = '';

        if (is_array($errorInformation) && array_key_exists('2', $errorInformation) && !is_null($errorInformation[2])) {
            $errorMessage = $errorInformation[2];
        }

        return $errorMessage;
    }

    /**
     * Quote the given string.
     *
     * @param string $value The string we want to quote.
     *
     * @return string The given string in quotes.
     */
    public function qstr($value)
    {
        return $this->quote($value);
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
     * @return bool
     */
    public function startTransaction()
    {
        try {
            $this->getConnection()->beginTransaction();
            $result = true;
        } catch (DBALException $exception) {
            $this->logException($exception);
            $result = false;
        }

        return $result;
    }

    /**
     * Commit a mysql transaction.
     *
     * @return bool
     */
    public function commitTransaction()
    {
        try {
            $this->getConnection()->commit();
            $result = true;
        } catch (DBALException $exception) {
            $this->logException($exception);
            $result = false;
        }

        return $result;
    }

    /**
     * Rollback a mysql transaction.
     *
     * @return bool
     */
    public function rollbackTransaction()
    {
        try {
            $this->getConnection()->rollBack();
            $result = true;
        } catch (DBALException $exception) {
            $this->logException($exception);
            $result = false;
        }

        return $result;
    }

    /**
     * Set the transaction isolation level.
     *
     * Allowed values are 'READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ', 'SERIALIZABLE'.
     *
     * @param string $level The level of transaction isolation we want to set.
     *
     * @return bool Was the setting of transaction isolation level successful?
     */
    public function setTransactionIsolationLevel($level = null)
    {
        $result = false;

        if (!array_key_exists(strtoupper($level), $this->transactionIsolationLevelMap)) {
            return $result;
        }

        try {
            $result = (bool) $this->getConnection()->setTransactionIsolation($this->transactionIsolationLevelMap[$level]);
        } catch (DBALException $exception) {
            $this->logException($exception);
        }

        return $result;
    }

    /**
     * Execute the given query and return the corresponding result set.
     *
     * @param string     $query      The query we want to execute.
     * @param array|bool $parameters The parameters for the given query.
     *
     * @throws DatabaseException
     *
     * @return DoctrineEmptyResultSet|DoctrineResultSet
     */
    public function execute($query, $parameters = false)
    {
        // we divide the execution here, cause it is easier to achieve the adodblite behavior this way
        if ($this->isSelectStatement($query)) {
            $result = $this->select($query, $parameters);
        } else {
            $result = $this->executeUpdate($query, $parameters);
        }

        return $result;
    }

    /**
     * Run a given select sql statement on the database.
     *
     * @param string $query      The query we want to execute.
     * @param bool   $parameters The parameters for the given query.
     * @param bool   $type
     *
     * @throws \Doctrine\DBAL\DBALException The exception, that can occur while running the sql statement.
     *
     * @return DoctrineResultSet The result of the given query.
     */
    public function select($query, $parameters = false, $type = true)
    {
        $result = null;
        $parameters = $this->assureParameterIsAnArray($parameters);
        try {
            /**
             * Be aware that Connection::executeQuery is a method specifically for READ operations only.
             * This is especially important in master-slave Connection
             */
            /** @var \Doctrine\DBAL\Driver\Statement $statement */
            $statement = $this->getConnection()->executeQuery($query, $parameters);
            $this->setAffectedRows(0);

            $result = new DoctrineResultSet($statement);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

    /**
     * Executes an SQL INSERT/UPDATE/DELETE query with the given parameters, sets the number of affected rows and returns
     * an empty DoctrineResultSet.
     *
     * This method supports PDO binding types as well as DBAL mapping types.
     *
     * @param string $query  The SQL query.
     * @param array  $params The query parameters.
     * @param array  $types  The parameter types.
     *
     * @throws DBALException
     *
     * @return DoctrineEmptyResultSet
     */
    protected function executeUpdate($query, $params = array(), $types = array())
    {
        if (!$params) {
            $params = array();
        }
        $affectedRows = $this->getConnection()->executeUpdate($query, $params, $types);

        $this->setAffectedRows($affectedRows);

        $result = new DoctrineEmptyResultSet();

        return $result;
    }

    /**
     * Run a given select sql statement with a limit clause on the database.
     *
     * @param string     $query      The sql statement we want to execute.
     * @param int        $limit      Number of rows to select
     * @param int        $offset     Number of rows to skip
     * @param array|bool $parameters The parameters array.
     * @param bool       $type       Connection type
     *
     * @return DoctrineResultSet The result of the given query.
     */
    public function selectLimit($query, $limit = -1, $offset = -1, $parameters = false, $type = true)
    {
        $limitSql = "";
        if (-1 !== $limit) {
            $limitSql = "LIMIT $limit";
        }

        $offsetSql = "";
        if ((-1 !== $offset) && (-1 !== $limit)) {
            $offsetSql = "OFFSET $offset";
        }

        return $this->select($query . " $limitSql $offsetSql", $parameters, $type);
    }

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
    public function getCol($query, $parameters = false, $executeOnSlave = true)
    {
        $parameters = $this->assureParameterIsAnArray($parameters);

        $rows = $this->getConnection()->fetchAll($query, $parameters);

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
     * Create the database connection.
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return \Doctrine\DBAL\Connection The database connection.
     */
    protected function createConnection()
    {
        $connection = DriverManager::getConnection($this->getConnectionParameters());

        $connection->setFetchMode($this->fetchMode);

        return $connection;
    }

    /**
     * Get the connection parameter array.
     *
     * @todo: Map the iDebug config.inc parameter to the doctrine settings.
     *
     * @return array The connection settings parameters.
     */
    private function getConnectionParameters()
    {
        $config = $this->getConfig();

        $connectionParameters = array(
            'dbname'   => $config->getConfigParam('dbName'),
            'user'     => $config->getConfigParam('dbUser'),
            'password' => $config->getConfigParam('dbPwd'),
            'host'     => $config->getConfigParam('dbHost'),
            'driver'   => $this->mapConnectionParameterDriver($config->getConfigParam('dbType'))
        );

        if ($config->getConfigParam('iUtfMode')) {
            $connectionParameters['driverOptions'] = array(
                1002 => 'SET NAMES utf8'
            );
        }

        return $connectionParameters;
    }

    /**
     * Map the driver name from the config to the doctrine driver name.
     *
     * @param string $configDriver The driver name from the config.
     *
     * @return string The doctrine driver name.
     */
    private function mapConnectionParameterDriver($configDriver)
    {
        $doctrineDriver = $configDriver;

        if ('mysql' == $doctrineDriver) {
            $doctrineDriver = 'pdo_' . $configDriver;
        }

        return $doctrineDriver;
    }

    /**
     * Sanitize the given parameter to be an array.
     *
     * @param bool|array $parameter The parameter we want to be an array.
     *
     * @return array An empty array.
     */
    private function assureParameterIsAnArray($parameter)
    {
        if (!$parameter) {
            $parameter = array();
        }

        return $parameter;
    }

    /**
     * Check, if the given sql query is a select statement.
     *
     * @param string $query The query we want to check.
     *
     * @return bool Is the given query a select statement?
     */
    private function isSelectStatement($query)
    {
        $formedQuery = strtoupper(trim($query));

        return 0 === strpos($formedQuery, 'SELECT');
    }

    /**
     * Convert a given native Doctrine exception into an OxidEsales exception.
     *
     * @todo: add test!
     *
     * @param \Exception $exception Doctrine exception to be converted
     *
     * @return \Exception Converted exception
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
            default:
                $exceptionClass = 'OxidEsales\Eshop\Core\exception\DatabaseException';
        }


        return new $exceptionClass($message, $code, $exception);
    }

    /**
     * Handle a given exception
     *
     * @todo: add test!
     *
     * @param \Exception $exception
     *
     * @throws \Exception|\oxConnectionException|DatabaseException
     */
    protected function handleException(\Exception $exception)
    {
        $this->logException($exception);

        throw $exception;
    }

    /**
     * Log a given Exception
     *
     * @todo: add test!
     *
     * @param \Exception $exception
     */
    protected function logException(\Exception $exception)
    {

        $message = $exception->getCode() . ' ' . $exception->getMessage();
        $context = array(
            'exception'         => $exception,
            'previousException' => $exception->getPrevious()
        );
        $this->logger->error($message, $context);
    }

    /**
     * Get all values as array.
     * Alias of getArray.
     *
     * @param string     $query
     * @param array|bool $parameters     Array of parameters
     * @param bool       $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @see Doctrine::getArray()
     *
     * @throws     DatabaseException
     *
     * @return array
     */
    public function getAll($query, $parameters = array(), $executeOnSlave = true)
    {
        try {
            $result = $this->getArray($query, $parameters, $executeOnSlave);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        return $result;
    }

    /**
     * Get all values as an array.
     * The format of returned the array depends on the fetch mode.
     * Set the desired fetch mode with DatabaseInterface::setFetchMode() before calling this method.
     * The default fetch mode is defined in Doctrine::$fetchMode
     *
     * @param string     $query
     * @param array|bool $parameters     Array of parameters
     * @param bool       $executeOnSlave Execute this statement on the slave database. Only evaluated in a master - slave setup.
     *
     * @see DatabaseInterface::setFetchMode()
     * @see Doctrine::$fetchMode
     *
     * @throws     DatabaseException
     *
     * @return array
     */
    public function getArray($query, $parameters = array(), $executeOnSlave = true)
    {

        if ($parameters && !is_array($parameters)) {
            throw new \InvalidArgumentException();
        }

        try {
            $statement = $this->getConnection()->executeQuery($query, $parameters);
        } catch (DBALException $exception) {
            $exception = $this->convertException($exception);
            $this->handleException($exception);
        }

        $result = $statement->fetchAll();

        return $result;
    }
}
