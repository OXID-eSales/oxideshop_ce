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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once 'oxerptype.php';

/**
 * Order Article erp type subclass
 */
class oxERPType_OrderArticle extends oxERPType
{
    /**
     * class constructor
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();

        $this->_sTableName = 'oxorderarticles';
        $this->_sShopObjectName = 'oxorderarticle';
    }

    /**
     * returns Sql for export
     *
     * @param string $sWhere    where part of sql
     * @param int    $iLanguage language id
     * @param int    $iShopID   shop id
     *
     * @see objects/oxERPType#getSQL()
     *
     * @return string
     */
    public function getSQL( $sWhere, $iLanguage = 0, $iShopID = 1)
    {
        if ( strstr( $sWhere, 'where')) {
            $sWhere .= ' and ';
        } else {
            $sWhere .= ' where ';
        }

        $sWhere .= 'oxordershopid = \''.$iShopID.'\'';

        return parent::getSQL($sWhere, $iLanguage, $iShopID);
    }

    /**
     * check for write access for id
     *
     * @param oxBase $oObj  loaded shop object
     * @param array  $aData fields to be written, null for default
     *
     * @throws Exception on now access
     *
     * @return null
     */
    public function checkWriteAccess($oObj, $aData = null)
    {
            return;

        if ($oObj->oxorderarticles__oxordershopid->value != oxRegistry::getConfig()->getShopId()) {
            throw new Exception( oxERPBase::$ERROR_USER_NO_RIGHTS);
        }

        parent::checkWriteAccess($oObj, $aData);
    }

    /**
     * return sql column name of given table column
     *
     * @param string $sField    field name
     * @param int    $iLanguage language id
     * @param int    $iShopID   shop id
     *
     * @return string
     */
    protected function _getSqlFieldName($sField, $iLanguage = 0, $iShopID = 1)
    {
            switch ($sField) {
                case 'OXORDERSHOPID':
                    return "'1' as $sField";
            }

        return parent::_getSqlFieldName($sField, $iLanguage, $iShopID);
    }

    /**
     * issued before saving an object. can modify aData for saving
     *
     * @param oxBase $oShopObject         oxBase child for object
     * @param array  $aData               data for object
     * @param bool   $blAllowCustomShopId if true then AllowCustomShopId
     *
     * @return array
     */
    protected function _preAssignObject($oShopObject, $aData, $blAllowCustomShopId)
    {
        $aData = parent::_preAssignObject($oShopObject, $aData, $blAllowCustomShopId);

        // check if data is not serialized
        $aPersVals = @unserialize($aData['OXPERSPARAM']);
        if (!is_array($aPersVals)) {
            // data is a string with | separation, prepare for oxid
            $aPersVals = explode("|", $aData['OXPERSPARAM']);
            $aData['OXPERSPARAM'] = serialize($aPersVals);
        }

            if (isset($aData['OXORDERSHOPID'])) {
                $aData['OXORDERSHOPID'] = 'oxbaseshop';
            }

        return $aData;
    }
}
