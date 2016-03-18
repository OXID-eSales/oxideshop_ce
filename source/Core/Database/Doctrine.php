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

        return $errorInformation[1];
    }

    /**
     * Get the last error message, occurred while executing a sql statement through any of the methods in this class.
     *
     * @return string The last error message.
     */
    public function errorMsg()
    {
        $errorInformation = $this->getConnection()->errorInfo();

        return $errorInformation[2];
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
        if (!$parameters) {
            $parameters = array();
        }

        return new DoctrineResultSet(
            $this->getConnection()->executeQuery($query, $parameters)
        );
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
        return DriverManager::getConnection($this->getConnectionParameters());
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

}
