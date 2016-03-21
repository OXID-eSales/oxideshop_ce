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

use Doctrine\DBAL\DriverManager;
use OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet;
use OxidEsales\Eshop\Core\Database\DoctrineEmptyResultSet;
use oxLegacyDb;
use PDO;

/**
 * The doctrine implementation of our database.
 *
 * @package OxidEsales\Eshop\Core\Database
 */
class Doctrine extends oxLegacyDb
{

    /**
     * @var \Doctrine\DBAL\Connection The database connection.
     */
    protected $connection = null;

    /**
     * @var int The number of rows affected by the last sql query.
     */
    protected $affectedRows = 0;

    /**
     * @var int The last fetch mode. We store the adodblite fetch mode here. See mapFetchMode method for further information.
     */
    protected $fetchMode = 2;

    /**
     * The standard constructor.
     */
    public function __construct()
    {
        $this->setConnection($this->createConnection());
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
     * Set the fetch mode for future calls. Returns the old fetch mode.
     *
     * Hints:
     *  - we map the adodb fetch mode to the pdo (used by doctrine) fetch mode here
     *  - cause there is no getter in dbal or pdo we save the actual fetch mode in this object too
     *
     * @param int $fetchMode How do we want to get the results?
     *
     * @return int The previous fetch mode.
     */
    public function setFetchMode($fetchMode)
    {
        $lastFetchMode = $this->fetchMode;

        $newFetchMode = $this->mapFetchMode($fetchMode);

        $this->getConnection()->setFetchMode($newFetchMode);
        $this->fetchMode = $newFetchMode;

        return $lastFetchMode;
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

        if (array_key_exists('1', $errorInformation) && !is_null($errorInformation[1])) {
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

        if (array_key_exists('2', $errorInformation) && !is_null($errorInformation[2])) {
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
     * @todo: find out what the return value means
     *
     * @return bool
     */
    public function startTransaction()
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit a mysql transaction.
     *
     * @todo: find out what the return value means
     *
     * @return bool
     */
    public function commitTransaction()
    {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback a mysql transaction.
     *
     * @todo: find out what the return value means
     *
     * @return bool
     */
    public function rollbackTransaction()
    {
        return $this->getConnection()->rollBack();
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

        $availableLevels = array('READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ', 'SERIALIZABLE');
        if (in_array(strtoupper($level), $availableLevels)) {
            $result = $this->execute("SET TRANSACTION ISOLATION LEVEL $level;");
        }

        return $result;
    }

    /**
     * Execute the given query and return the corresponding result set.
     *
     * @todo: implement and test switch, so that SELECT gets handled different (no empty result set)!
     *
     * @param string     $query      The query we want to execute.
     * @param array|bool $parameters The parameters for the given query.
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return mixed|DoctrineEmptyResultSet
     */
    public function execute($query, $parameters = false)
    {
        $affectedRows = $this->getConnection()->exec($query);

        $this->setAffectedRows($affectedRows);

        return new DoctrineEmptyResultSet();
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
        $parameters = $this->assureParameterIsAnArray($parameters);

        return new DoctrineResultSet(
            $this->getConnection()->executeQuery($query, $parameters)
        );
    }

    /**
     * Run a given select sql statement with a limit clause on the database.
     *
     * @param string     $query        The sql statement we want to execute.
     * @param int        $numberOfRows Number of rows to select
     * @param int        $offset       Number of rows to skip
     * @param array|bool $parameters   The parameters array.
     * @param bool       $type         Connection type
     *
     * @return DoctrineResultSet The result of the given query.
     */
    public function selectLimit($query, $numberOfRows = -1, $offset = -1, $parameters = false, $type = true)
    {
        $offsetSql = "";
        if (-1 !== $offset) {
            $offsetSql = "OFFSET $offset";
        }
        return $this->select($query . " LIMIT $numberOfRows $offsetSql", $parameters, $type);
    }

    /**
     * Get the values of a column.
     *
     * @param string     $query      The sql statement we want to execute.
     * @param array|bool $parameters The parameters array.
     * @param bool       $onMaster   Do we want to execute this statement on the master?
     *
     * @return array The values of a column of a corresponding sql query.
     */
    public function getCol($query, $parameters = false, $onMaster = true)
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
     * @return \Doctrine\DBAL\Connection The dataabase connection.
     */
    protected function createConnection()
    {
        $connection = DriverManager::getConnection($this->getConnectionParameters());

        $connection->setFetchMode($this->fetchMode);

        return $connection;
    }

    /**
     * Map the adodb lite fetch mode to the corresponding pdo fetch mode.
     *
     *  ADODB_FETCH_DEFAULT = 0
     *  ADODB_FETCH_NUM = 1
     *  ADODB_FETCH_ASSOC = 2
     *  ADODB_FETCH_BOTH = 3
     *
     *  FETCH_LAZY = 1
     *  FETCH_ASSOC = 2
     *  FETCH_NUM = 3
     *  FETCH_BOTH = 4
     *
     * @param int $fetchMode The adodb fetch mode.
     *
     * @return int The pdo fetch mode.
     */
    private function mapFetchMode($fetchMode)
    {
        $result = $fetchMode + 1;

        if (1 === $fetchMode) {
            $result = 3;
        }
        if (2 === $fetchMode) {
            $result = 2;
        }

        return $result;
    }

    /**
     * Get the connection parameter array.
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

}
