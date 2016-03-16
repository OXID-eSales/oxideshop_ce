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
     * Create the database connection.
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return \Doctrine\DBAL\Connection The dataabase connection.
     */
    protected function createConnection()
    {
        $connection = DriverManager::getConnection($this->getConnectionParameters());

        return $connection;
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
            // @todo: check, if it is necessary, to map the ADODbLite driver type into doctrine
            'driver'   => 'pdo_' . $config->getConfigParam('dbType')
        );

        if ($config->getConfigParam('iUtfMode')) {
            $connectionParameters['driverOptions'] = array(
                1002 => 'SET NAMES utf8'
            );
        }

        return $connectionParameters;
    }

}
