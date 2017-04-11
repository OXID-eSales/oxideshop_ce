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

/**
 * Class ObjectCaller
 */
class ObjectConstructor
{
    /**
     * @var object
     */
    protected $_oObject = null;

    /**
     * @param $sClassName
     */
    public function __construct($sClassName)
    {
        $this->_oObject = $this->_createObject($sClassName);
    }

    /**
     * Returns constructed object
     *
     * @return object
     */
    public function getObject()
    {
        return $this->_oObject;
    }

    /**
     * Loads object by given id
     *
     * @param mixed $mOxId
     * @throws Exception
     */
    public function load($mOxId)
    {
        if (!empty($mOxId)) {
            $blResult = is_array($mOxId)? $this->_loadByArray($mOxId) : $this->_loadById($mOxId);
            if ($blResult === false) {
                $sClass = get_class($this->getObject());
                throw new Exception("Failed to load $sClass with id $mOxId");
            }
        }
    }

    /**
     * @param string $sOxId
     */
    protected function _loadById($sOxId)
    {
        if ($sOxId == 'lastInsertedId') {
            $sOxId = $this->_getLastInsertedId();
        }
        return $this->getObject()->load($sOxId);
    }

    /**
     * @param array $aOxId
     */
    protected function _loadByArray($aOxId)
    {
        $sFunction = key($aOxId);
        $sId = current($aOxId);
        return $this->getObject()->$sFunction($sId);
    }

    /**
     * Sets class parameters
     *
     * @param array $aClassParams
     * @return array
     */
    public function setClassParameters($aClassParams)
    {
        $oObject = $this->getObject();
        $sTableName = $this->_getTableName(get_class($oObject));
        $aValues = array();
        foreach ($aClassParams as $sParamKey => $sParamValue) {
            if (is_int($sParamKey)) {
                $sFieldName = $this->_getFieldName($sTableName, $sParamValue);
                $aValues[$sParamValue] = $oObject->$sFieldName->value;
            } else {
                $sFieldName = $this->_getFieldName($sTableName, $sParamKey);
                if (is_string($sParamValue)) {
                    $sParamValue = html_entity_decode($sParamValue);
                }
                $oObject->$sFieldName = new oxField($sParamValue);
            }
        }

        return $aValues;
    }

    /**
     * Calls object function with given parameters
     *
     * @param $sFunction
     * @param $aParameters
     * @return mixed
     */
    public function callFunction($sFunction, $aParameters)
    {
        $aParameters = is_array($aParameters) ? $aParameters : array();
        $mResponse = call_user_func_array(array($this->getObject(), $sFunction), $aParameters);
        $this->clearCache();

        return $mResponse;
    }

    /**
     * flush cache if needed
     */
    public function clearCache()
    {
    }

    /**
     * Returns created object to work with
     *
     * @param $sClassName
     * @return object
     */
    protected function _createObject($sClassName)
    {
        return new $sClassName();
    }

    /**
     * Return table name from class name.
     * @example $sClassName = oxArticle; return oxarticles;
     * @example $sClassName = oxRole; return oxroles;
     *
     * @param $sClass
     *
     * @return string
     */
    protected function _getTableName($sClass)
    {
        $aClassNameWithoutS = array("oxarticle", "oxcounter", "oxrole", "oxfile", "oxrating", "oxreview", "oxrecommlist", "oxshop",
                                    "oxmanufacturer", "oxmediaurl", "oxvoucherserie", "oxorderarticle", "oxorderfile", "oxpayment",
                                    "oxuserbasketitem", "oxuserbasket", "oxuserpayment", "oxvoucher", "oxvoucherserie", "oxcontent");
        $aClassNameWithoutIes = array("oxcategory", "oxgbentry");

        $sClassName = $sTableName = strtolower($sClass);
        if (in_array($sClassName, $aClassNameWithoutS)) {
            $sTableName = $sClassName . "s";
        } elseif (in_array($sClassName, $aClassNameWithoutIes)) {
            $sTableName = substr($sClassName, 0, -1) . "ies";
        }

        return $sTableName;
    }

    /**
     * @param $sTableName
     * @param $sParamValue
     * @return string
     */
    protected function _getFieldName($sTableName, $sParamValue)
    {
        $sResult = $sTableName . '__' . $sParamValue;
        if (strpos($sParamValue, '__') !== false) {
            $sResult = $sParamValue;
        }
        return strtolower($sResult);
    }

    /**
     * Get id of latest created row.
     *
     * @return string|null
     */
    protected function _getLastInsertedId()
    {
        $sOxid = null;
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $sTableName = $this->_getTableName(get_class($this->getObject()));
        $sSql = 'SELECT OXID FROM '. $sTableName .' ORDER BY OXTIMESTAMP DESC LIMIT 1';
        $rs = $oDb->select($sSql);

        if ($rs != false && $rs->recordCount() > 0) {
            $aFields = $rs->fields;
            $sOxid = $aFields['OXID'];
        }

        return $sOxid;
    }

}
