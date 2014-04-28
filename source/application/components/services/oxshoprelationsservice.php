<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vilma-oxid
 * Date: 14.4.24
 * Time: 13.21
 * To change this template use File | Settings | File Templates.
 */

class oxShopRelationsService
{

    /**
     * @var oxShopRelations $_oShopRelations Shop relations object
     */
    protected $_oShopRelations = null;

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
        $aAvailableSubShops = $oItem->getAvailableShopIds();

        $aCurrentShop = array($this->_getItemShopId());
        $aAvailableSubShops = array_diff($aAvailableSubShops, $aCurrentShop);
        $aShopIds = array_merge($aSelectedSubShops, $aAvailableSubShops);
        $aShopIds = array_unique($aShopIds);
        foreach ($aShopIds as $iShopId) {
            //naturally inherited(+), but not select from form input(-)
            if (in_array($iShopId, $aAvailableSubShops) && !in_array($iShopId, $aSelectedSubShops)) {
                $oItem->unassignFromShop(null, $iShopId);
            }

            //naturally not inherited(-) and selected (+)
            if (!in_array($iShopId, $aAvailableSubShops) && in_array($iShopId, $aSelectedSubShops)) {
                $oItem->assignToShop(null, $iShopId);
            }
        }
    }

    /**
     * Sets shop relations object
     *
     * @param oxShopRelations $oShopRelations Shop relations object
     */
    public function setShopRelations($oShopRelations)
    {
        $this->_oShopRelations = $oShopRelations;
    }

    /**
     * Returns shop relations object. Creates shop relations object for base shop
     *
     * @return oxShopRelations
     */
    public function getShopRelations()
    {
        if (is_null($this->_oShopRelations)) {
            $this->_oShopRelations = new oxShopRelations(1);
        }
        return $this->_oShopRelations;
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
    public function setObjectClassName( $sObjectClassName )
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
    public function setEditObjectId( $sEditObjectId )
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
    public function setItemShopId( $sShopId )
    {
        $this->_sItemShopId = $sShopId;
    }
}