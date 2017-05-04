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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once 'interface/DatabaseInterface.php';

/**
 * Database connection class
 *
 * @deprecated since v5.3.0 (2016-04-19); This class will be removed. There will be a DatabaseInterface in v6.0 which
 *             includes all but the deprecated methods of oxLegacyDb. An implementation of the DatabaseInterface based
 *             on Doctrine DBAL will replace oxLegacyDb.
 *
 * @implements DatabaseInterface
 *
 */
class oxLegacyDb extends oxSuperCfg implements DatabaseInterface
{

    /**
     * Database connection object
     *
     * @var object
     */
    protected $_oDb = null;


    /**
     * Set connection
     *
     * @param object $oConnection Connection
     *
     * @deprecated since v5.3.0 (2016-04-21); This method will be protected in v6.0. Do not use any more.
     */
    public function setConnection($oConnection)
    {
        $this->_oDb = $oConnection;
    }

    /**
     * @inheritdoc
     */
    public function forceMasterConnection()
    {

    }

    /**
     * @inheritdoc
     */
    public function setFetchMode($iFetchMode)
    {
        $this->_oDb->setFetchMode($iFetchMode);

    }

    /**
     * Return connection to db
     *
     * @param bool $blType - connection type
     *
     * @deprecated since v5.3.0 (2016-04-14); This method will be protected in v6.0. Do not use any more.
     *
     * @return object
     */
    public function getDb($blType = true)
    {

        return $this->_oDb;
    }

    /**
     * @inheritdoc
     */
    public function getOne($sSql, $aParams = array(), $blType = true)
    {
        return $this->getDb($blType)->getOne($sSql, $aParams);
    }

    /**
     * Get value
     *
     * @param string     $sSql    Query
     * @param array      $aParams Array of parameters
     * @param bool       $blType  connection type
     *
     * @deprecated since v5.3.0 (2016-04-14); Use the method oxLegacyDb::getAll() instead.
     *
     * @return array
     */
    public function getArray($sSql, $aParams = array(), $blType = true)
    {
        return $this->getDb($blType)->getArray($sSql, $aParams);
    }

    /**
     * @inheritdoc
     */
    public function getRow($sSql, $aParams = array(), $blType = true)
    {
        return $this->getDb($blType)->getRow($sSql, $aParams);
    }

    /**
     * @inheritdoc
     */
    public function getAll($sSql, $aParams = array(), $blType = true)
    {

        return $this->getDb($blType)->getAll($sSql, $aParams);
    }

    /**
     * @inheritdoc
     */
    public function select($sSql, $aParams = array(), $blType = true)
    {
        return $this->getDb($blType)->execute($sSql, $aParams);
    }

    /**
     * Get value
     *
     * @param string     $sSql    Query
     * @param array      $aParams Array of parameters
     * @param bool       $blType  connection type
     *
     * @deprecated since v5.3.0 (2016-04-14); This method will be removed. Rebuild the functionality of this
     * method with your code using getAll with assoc fetch mode. In some use cases you should be able to simply use getRow
     *
     * @return array
     */
    public function getAssoc($sSql, $aParams = array(), $blType = true)
    {
        return $this->getDb($blType)->getAssoc($sSql, $aParams);
    }

    /**
     * @inheritdoc
     */
    public function getCol($sSql, $aParams = array(), $blType = true)
    {
        return $this->getDb($blType)->getCol($sSql, $aParams);
    }

    /**
     * @inheritdoc
     */
    public function selectLimit($sSql, $iRows = -1, $iOffset = -1, $aParams = array(), $blType = true)
    {
        return $this->getDb($blType)->SelectLimit($sSql, $iRows, $iOffset, $aParams);
    }

    /**
     * @inheritdoc
     */
    public function execute($sSql, $aParams = array())
    {
        return $this->getDb(false)->execute($sSql, $aParams);
    }

    /**
     * Execute query
     *
     * @param string     $sSql    Query
     * @param array      $aParams Array of parameters
     *
     * @deprecated since v5.3.0 (2016-04-15); This method will be removed in v6.0. Please use the method execute() instead.
     *
     * @return object
     */
    public function query($sSql, $aParams = array())
    {
        return $this->getDb(false)->Query($sSql, $aParams);
    }

    /**
     * Returns the count of rows affected by the last query.
     * This is an alias for affectedRows().
     *
     * @deprecated since v5.3.0 (2016-04-14); This method will be removed in v6.0. Use the return value of execute() instead.
     *
     * @return int
     */
    public function Affected_Rows()
    {
        return $this->getDb(false)->Affected_Rows();
    }

    /**
     * Return error number
     *
     * @deprecated since v5.3.0 (2016-04-14); This information will be part of the thrown DatabaseException exception.
     *             Replace usage by catching DatabaseException and using DatabaseException->getCode()
     *
     * @return int
     */
    public function errorNo()
    {
        return $this->getDb(false)->ErrorNo();
    }

    /**
     * Return error message
     *
     * @deprecated since v5.3.0 (2016-04-14); This information will be part of the thrown DatabaseException exception.
     *             Replace usage by catching DatabaseException and using DatabaseException->getMessage()
     *
     * @return string
     */
    public function errorMsg()
    {
        return $this->getDb(false)->ErrorMsg();
    }

    /**
     * Quote string
     *
     * @param string $sValue value
     *
     * @deprecated since v5.3.0 (2016-04-14); Use the method oxLegacyDb::quote() instead.
     *
     * @return string
     */
    public function qstr($sValue)
    {
        return $this->getDb(false)->qstr($sValue);
    }

    /**
     * @inheritdoc
     */
    public function quote($sValue)
    {
        return $this->getDb(false)->quote($sValue);
    }

    /**
     * @inheritdoc
     */
    public function quoteArray($aStrArray)
    {
        foreach ($aStrArray as $sKey => $sString) {
            $aStrArray[$sKey] = $this->quote($sString);
        }

        return $aStrArray;
    }

    /**
     * @inheritdoc
     */
    public function metaColumns($sTable)
    {
        return $this->getDb(false)->MetaColumns($sTable);
    }

    /**
     * return meta data
     *
     * @param string $sTable       Table name
     * @param bool   $blNumIndexes Numeric indexes
     *
     * @deprecated since v5.3.0 (2016-04-13); Not used. In v6.0 this method will be removed.
     *
     * @return array
     */
    public function metaColumnNames($sTable, $blNumIndexes = false)
    {
        return $this->getDb(false)->MetaColumnNames($sTable, $blNumIndexes);
    }

    /**
     * @inheritdoc
     */
    public function startTransaction()
    {
        return $this->getDb(false)->execute('START TRANSACTION');
    }

    /**
     * @inheritdoc
     */
    public function commitTransaction()
    {
        return $this->getDb(false)->execute('COMMIT');
    }

    /**
     * @inheritdoc
     */
    public function rollbackTransaction()
    {
        return $this->getDb(false)->execute('ROLLBACK');
    }

    /**
     * @inheritdoc
     */
    public function setTransactionIsolationLevel($sLevel = null)
    {
        $blResult = false;

        $aLevels = array('READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ', 'SERIALIZABLE');
        if (in_array(strtoupper($sLevel), $aLevels)) {
            $blResult = $this->getDb(false)->execute('SET TRANSACTION ISOLATION LEVEL ' . $sLevel);
        }

        return $blResult;
    }

    /**
     * Calls Db UI method
     *
     * @param integer $iPollSecs poll seconds
     *
     * @deprecated since v5.3.0 (2016-06-07); This method will be removed in v6.0.
     */
    public function UI($iPollSecs = 5)
    {
        $this->getDb(false)->UI($iPollSecs);
    }

    /**
     * Returns last insert ID.
     * This method is an alias of getLastInsertId().
     *
     * @deprecated since v5.3.0 (2016-04-14); This method will be removed in v6.0. Use lastInsertId instead.
     *
     * @return int
     */
    public function Insert_ID()
    {
        return $this->getLastInsertId();
    }

    /**
     * Returns last insert ID
     * This method is an alias of getLastInsertId().
     *
     * @deprecated since v5.3.2 (2016-10-12); This method will be removed in v6.0. Use getLastInsertId instead.
     *
     * @return int
     */
    public function lastInsertId()
    {
        return $this->getLastInsertId();
    }

    /**
     * Returns last insert ID
     *
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->getDb(false)->Insert_ID();
    }
}
