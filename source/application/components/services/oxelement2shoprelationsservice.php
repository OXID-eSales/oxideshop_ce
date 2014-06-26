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

class oxElement2ShopRelationsService
{
    /**
     * DB table having oxshopincl and oxshopexlc field we are going to deal with
     */
    protected $_sMallTable = null;

    /**
     * Original item shopid, (eg oxarticle__oxshopid->value)
     */
    protected $_sItemShopId = null;

    /**
     * @var array Selected subshops
     */
    protected $_aSelectedSubshops = null;

    /**
     * Class name of object to load
     */
    protected $_sObjectClassName = null;

    /**
     * Object id, that was edited
     */
    protected $_sEditObjectId = null;

    /**
     * Assigns record information in multiple shop field
     *
     * @return null
     */
    public function assignToSubShops()
    {
        $aSelectedSubShops = $this->getSelectedSubShops();
        $oItem = $this->_getSelectedItem();
        $aItemShopIds = $oItem->getItemAssignedShopIds();
        $aAllSubShops = $this->getSubShopList($this->_getItemShopId());

        foreach ($aAllSubShops as $oSubShop) {
            $iShopId = $oSubShop->getId();
            //naturally inherited(+), but not select from form input(-)
            if (in_array($iShopId, $aItemShopIds) && !in_array($iShopId, $aSelectedSubShops)) {
                $oItem->unassignFromShop($iShopId);
            }

            //naturally not inherited(-) and selected (+)
            if (!in_array($iShopId, $aItemShopIds) && in_array($iShopId, $aSelectedSubShops)) {
                $oItem->assignToShop($iShopId);
            }
        }
    }

    /**
     * Returns subshop tree.
     *
     * @param string $sShopId shop id
     *
     * @return null
     */
    public function getSubShopList($sShopId)
    {
        /** @var oxShop $oShop */
        $oActShop = oxNew('oxShop');
        $oActShop->load($sShopId);
        $oShopList = $oActShop->getSubShopList();
        return $oShopList;
    }

    /**
     * Loads selected item using oxBase
     *
     * @return oxBase
     */
    protected function _getSelectedItem()
    {
        $sObjectClassName = $this->_getObjectClassName();
        $oItem = oxNew($sObjectClassName);
        $oItem->init($this->_getMallTable());
        $oItem->load($this->getEditObjectId());
        return $oItem;
    }

    /**
     * Returns selected subshops
     *
     * @return mixed
     */
    public function getSelectedSubShops()
    {
        return $this->_aSelectedSubshops;
    }

    /**
     * Returns array of selected subshop ids
     *
     * @param array $aSelectedSubShops Array of shop ids, that were selected in admin.
     */
    public function setSelectedSubShops($aSelectedSubShops)
    {
        $this->_aSelectedSubshops = $aSelectedSubShops;
    }

    /**
     * Returns object class name
     *
     * @return string
     */
    protected function _getObjectClassName()
    {
        return $this->_sObjectClassName;
    }

    /**
     * Object class name setter
     *
     * @param string $sObjectClassName Object class name
     *
     * @return null
     */
    public function setObjectClassName($sObjectClassName)
    {
        $this->_sObjectClassName = $sObjectClassName;
    }

    /**
     * Returns mall table name
     *
     * @return string
     */
    protected function _getMallTable()
    {
        return $this->_sMallTable;
    }

    /**
     * Mall table name setter
     *
     * @param string $sMallTable Mall table name
     *
     * @return null
     */
    public function setMallTable( $sMallTable )
    {
        $this->_sMallTable = $sMallTable;
    }

    /**
     * Returns active/editable object id
     *
     * @return string
     */
    public function getEditObjectId()
    {
        return $this->_sEditObjectId;
    }

    /**
     * Returns active/editable object id
     *
     * @return string
     */
    public function setEditObjectId($sEditObjectId)
    {
        $this->_sEditObjectId = $sEditObjectId;
    }

    /**
     * Returns item shop id
     *
     * @return string
     */
    protected function _getItemShopId()
    {
        return $this->_sItemShopId;
    }

    /**
     * Item shop id setter
     *
     * @param string $sShopId item shop id
     */
    public function setItemShopId($sShopId)
    {
        $this->_sItemShopId = $sShopId;
    }
}