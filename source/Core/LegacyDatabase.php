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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\Eshop\Core;

use mysql_driver_ADOConnection as mysql_driver;
use mysql_extend_ADOConnection as mysql_extend;
use mysql_meta_ADOConnection as mysql_meta;
use mysqli_driver_ADOConnection as mysqli_driver;
use mysqli_extend_ADOConnection as mysqli_extend;
use mysqli_meta_ADOConnection as mysqli_extra;
use object_ADOConnection;
use object_ResultSet;
use pear_ADOConnection;
use OxidEsales\Eshop;

/**
 * Database connection class
 *
 * @deprecated since v5.3.0 (2016-04-19); This class will be removed. There will be a DatabaseInterface in v6.0 which
 *             includes all but the deprecated methods of oxLegacyDb. An implementation of the DatabaseInterface based
 *             on Doctrine DBAL will replace oxLegacyDb.
 *
 */
class LegacyDatabase extends \oxSuperCfg implements Eshop\Core\Database\DatabaseInterface
{
    /**
     * Database connection object
     *
     * @var mysql_driver_ADOConnection|mysql_extend_ADOConnection|mysql_meta_ADOConnection|mysqli_driver_ADOConnection|mysqli_extend_ADOConnection|mysqli_extra_ADOConnection|object_ADOConnection|pear_ADOConnection
     */
    protected $_oDb = null;

    /**
     * Set connection
     *
     * @param mysql_driver_ADOConnection|mysql_extend_ADOConnection|mysql_meta_ADOConnection|mysqli_driver_ADOConnection|mysqli_extend_ADOConnection|mysqli_extra_ADOConnection|object_ADOConnection|pear_ADOConnection $connection
     */
    public function setConnection($connection)
    {
        $this->_oDb = $connection;
    }

    /**
     * Set fetch mode to connection
     *
     * @param int $fetchMode Fetch mode
     */
    public function setFetchMode($fetchMode)
    {
        $this->_oDb->SetFetchMode($fetchMode);
    }

    /**
     * Return connection to db
     *
     * @param bool $type Connection type
     *
     * @return mysql_driver|mysql_extend|mysql_meta|mysqli_driver|mysqli_extend|mysqli_extra|object_ADOConnection|pear_ADOConnection
     */
    protected function getDb($type = true)
    {
        return $this->_oDb;
    }

    /**
     * Get one column, which you have to give into the sql select statement, of the first row, corresponding to the
     * given sql statement.
     *
     * @param string     $query
     * @param array|bool $parameters Array of parameters
     * @param bool       $type       Connection type
     *
     * @return string The first column of the first row, which is fitting to the given sql select statement.
     */
    public function getOne($query, $parameters = false, $type = true)
    {
        return $this->getDb($type)->GetOne($query, $parameters);
    }

    /**
     * Get one row.
     *
     * @param string $query
     * @param array  $parameters Array of parameters
     * @param bool   $type       Connection type
     *
     * @return array
     */
    public function getRow($query, $parameters = array(), $type = true)
    {
        return $this->getDb($type)->GetRow($query, $parameters);
    }

    /**
     * Get all values. The same as getArray.
     *
     * @param string $query
     * @param array  $parameters Array of parameters
     * @param bool   $type       Connection type
     *
     * @return array
     */
    public function getAll($query, $parameters = array(), $type = true)
    {
        return $this->getDb($type)->getAll($query, $parameters);
    }

    /**
     * Get value
     *
     * @param string $query
     * @param array  $parameters Array of parameters
     * @param bool   $type       Connection type
     *
     * @return mixed|Object_ResultSet
     */
    public function select($query, $parameters = array(), $type = true)
    {
        return $this->getDb($type)->Execute($query, $parameters);
    }

    /**
     * Get column value
     *
     * @param string $query
     * @param array  $parameters Array of parameters
     * @param bool   $type       Connection type
     *
     * @return array
     */
    public function getCol($query, $parameters = array(), $type = true)
    {
        return $this->getDb($type)->GetCol($query, $parameters);
    }

    /**
     * Get array
     *
     * @param string $query
     * @param int    $numberOfRows Number of rows to select
     * @param int    $offset       Number of rows to skip
     * @param array  $parameters   Array of parameters
     * @param bool   $type         Connection type
     *
     * @return mixed|Object_ResultSet
     */
    public function selectLimit($query, $numberOfRows = -1, $offset = -1, $parameters = array(), $type = true)
    {
        return $this->getDb($type)->SelectLimit($query, $numberOfRows, $offset, $parameters);
    }

    /**
     * Executes query and returns result set.
     *
     * @param string $query
     * @param array  $parameters Array of parameters
     *
     * @return mixed|Object_ResultSet
     */
    public function execute($query, $parameters = array())
    {
        return $this->getDb(false)->Execute($query, $parameters);
    }

    /**
     * Returns the count of rows affected by the last query.
     *
     * @return int
     */
    public function affectedRows()
    {
        return $this->getDb(false)->Affected_Rows();
    }

    /**
     * Quotes string.
     *
     * @param string $sValue value
     *
     * @return string
     */
    public function quote($sValue)
    {
        return $this->getDb(false)->Quote($sValue);
    }

    /**
     * Quotes an array.
     *
     * @param array $arrayOfStrings array of strings to quote
     *
     * @return array
     */
    public function quoteArray($arrayOfStrings)
    {
        foreach ($arrayOfStrings as $key => $string) {
            $arrayOfStrings[$key] = $this->quote($string);
        }

        return $arrayOfStrings;
    }

    /**
     * return meta data
     *
     * @param string $table Table name
     *
     * @return array
     */
    public function metaColumns($table)
    {
        return $this->getDb(false)->MetaColumns($table);
    }

    /**
     * Start mysql transaction.
     *
     * @return ADORecordSet_empty|object_ResultSet
     */
    public function startTransaction()
    {
        return $this->getDb(false)->Execute('START TRANSACTION');
    }

    /**
     * Commit mysql transaction.
     *
     * @return ADORecordSet_empty|object_ResultSet
     */
    public function commitTransaction()
    {
        return $this->getDb(false)->Execute('COMMIT');
    }

    /**
     * RollBack mysql transaction.
     *
     * @return ADORecordSet_empty|object_ResultSet
     */
    public function rollbackTransaction()
    {
        return $this->getDb(false)->Execute('ROLLBACK');
    }

    /**
     * Set transaction isolation level.
     * Allowed values READ UNCOMMITTED, READ COMMITTED, REPEATABLE READ, SERIALIZABLE
     *
     * @param string $level level
     *
     * @return bool|ADORecordSet_empty|object_ResultSet
     */
    public function setTransactionIsolationLevel($level = null)
    {
        $availableLevels = array('READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ', 'SERIALIZABLE');
        if (in_array(strtoupper($level), $availableLevels)) {
            return $this->getDb(false)->Execute('SET SESSION TRANSACTION ISOLATION LEVEL ' . $level);
        }
    }

    /**
     * Calls Db UI method
     *
     * @param integer $pollSeconds poll seconds
     */
    public function UI($pollSeconds = 5)
    {
        $this->getDb(false)->UI($pollSeconds);
    }

    /**
     * Returns last insert ID
     *
     * @return int
     */
    public function lastInsertId()
    {
        return $this->getDb(false)->Insert_ID();
    }
}
