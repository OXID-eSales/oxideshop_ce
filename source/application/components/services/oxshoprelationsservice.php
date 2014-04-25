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
     * Assigns record information in multiple shop field
     *
     * @return null
     */
    public function assignToSubshops()
    {
        $aAllSubShopList   = $this->_getSubShops();

        /** @var oxShop $oSubShop */
        foreach ($aAllSubShopList as $oSubShop) {
            $this->_handleRelations($oSubShop);
        }
    }

    /**
     * Handle shop relations for inheritable objects.
     * Assigns or unassigns object in given subshop
     *
     * @param oxShop $oSubShop loaded shop object
     */
    protected function _handleRelations($oSubShop)
    {
        $iShopId = $oSubShop->getId();
        $sMallTable = $this->_getMallTable();
        $aShopIds = array_merge(array($iShopId), $oSubShop->getInheritedSubshopList($sMallTable, $iShopId));
        $aShopIds = array_unique($aShopIds);

        $aSelectedSubShops = $this->getSelectedSubShops();
        $oItem = $this->_getSelectedItem();
        $this->getShopRelations()->setShopIds($aShopIds);

        //naturally inherited(+), but not select from form input(-)
        if ($oSubShop->selected && !in_array($iShopId, $aSelectedSubShops)) {
            $oItem->unassignFromShop();
        }

        //naturally not inherited(-) and selected (+)
        if (!$oSubShop->selected && in_array($iShopId, $aSelectedSubShops)) {
            $oItem->assignToShop();
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
     * Returns selected subshops
     *
     * @return mixed
     */
    public function setSelectedSubShops($aSelectedSubshops)
    {
        $this->_aSelectedSubshops = $aSelectedSubshops;
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
     * Returns subshop tree with marked selected shops.
     *
     * @param string $sShopID shop id
     *
     * @return null
     */
    protected function _getSubShops($sShopID = null)
    {
        $myConfig = $this->getConfig();

        /** @var oxBase $oItem */
        $oItem = oxNew('oxBase');
        $oItem->init($this->_getMallTable());
        $oItem->load($this->getEditObjectId());

        /** @var oxShopRelations $oShopRelations */
        $oShopRelations = oxNew('oxShopRelations', null);

        if ( !$sShopID ) {
            $sShopID = $myConfig->getShopID();
        }

        /** @var oxShop $oShop */
        $oActShop = oxNew('oxShop');
        $oActShop->load($sShopID);
        $oShopList = $oActShop->getSubShopList();

        //marking included shops
        foreach ( $oShopList as $key => $oShop ) {
            $oShopRelations->setShopIds($key);

            //marking items for multishops
            if ($oShopList[$key]->oxshops__oxismultishop->value) {
                $oShopList[$key]->selected = true;
            } else {
                //should we check the checkbox?
                $oShopList[$key]->selected = false;
            }

            //marking items included in shop
            if (!$oShopList[$key]->selected && $oShopRelations->isInShop($oItem->getMapId(), $this->_getMallTable())) {
                $oShopList[$key]->selected = true;
            }

            //marking items from inherited shops
            $iCurrent = $oShop->getId();
            //$blIsInherited = $oShopList[$iCurrent]->oxshops__oxisinherited->value;
            //determining inherited property by config option

            $blIsInherited = $this->_isTableInherited($oShopList[$iCurrent]);

            $i = 0;
            while ( $iCurrent && $blIsInherited && $i ++< 1000 ) {
                if ( $iCurrent == $sShopID || ( $iCurrent && $iCurrent == $this->_sItemShopId ) ) {
                    $oShopList[$key]->selected = true;
                }
                $blIsInherited = $this->_isTableInherited($oShopList[$iCurrent]);
                $iCurrent = $oShopList[$iCurrent]->oxshops__oxparentid->value;
            }

            // unmarking items excluded from shop
            if ($oShopList[$key]->selected && !$oShopRelations->isInShop($oItem->getMapId(), $this->_getMallTable())) {
                $oShopList[$key]->selected = false;
            }
        }

        return $oShopList;
    }

    /**
     * Checks if table $sTableName is inherited from parent in subshop $sShopId
     *
     * @param object $oShop shop object
     *
     * @return bool
     */
    protected function _isTableInherited( $oShop )
    {
        $blIsInherited = false;
        if ( ( $sMallTable = $this->_getMallTable() ) ) {
            $blVarVal = $this->getConfig()->getShopConfVar('blMallInherit_' . strtolower( $sMallTable ), $oShop->oxshops__oxid->value);
            if ( isset( $blVarVal ) ) {
                $blIsInherited = $blVarVal;
            }
        } else {
            $blIsInherited = $oShop->oxshops__oxisinherited->value;
        }

        return $blIsInherited;
    }

    /**
     * Returns subshop tree with marked selected shops.
     *
     * @param string $sShopId            shop id
     * @param bool   $blProcessSuperShop process supershop
     *
     * @return object
     */
    protected function _getSubShopTree( $sShopId = null, $blProcessSuperShop = true )
    {
        if ( !$sShopId ) {
            $sShopId = $this->getConfig()->getShopId();
        }

        //caching the result and preventing eternal recursion
        static $aShopIDs = array();
        if ( isset( $aShopIDs[$sShopId] ) ) {
            return $aShopIDs[$sShopId];
        }

        $oDb = oxDb::getDb();
        $blIsSupershop = $oDb->getOne( "select oxissupershop from oxshops where oxid = ".$oDb->quote( $sShopId ), false, false );
        $blAddCheck = ( $blIsSupershop && $blProcessSuperShop ) ?" oxid <> ".$oDb->quote( $sShopId )." and oxid <> ".$oDb->quote( $this->_sItemShopId )." ":"  oxparentid = ".$oDb->quote( $sShopId );
        //  dodger - Task #1574 - multishop should be removed from mall tabs
        $sQ = "select * from oxshops where oxismultishop = 0 and $blAddCheck";

        $oShopList = oxNew( "oxshoplist" );
        $oShopList->selectString($sQ);

        //selecting recursively
        if (!$blIsSupershop) {
            foreach ( $oShopList as $sKey => $oShop ) {
                $oSubShops = $this->_getSubShopTree( $sKey, false );
                foreach ($oSubShops as $sKey2 => $oShop2) {
                    if (!isset($oShopList[$sKey2])) {
                        $oShopList[$sKey2] = $oShop2;
                    }
                }
            }
        }
        return $oShopList;
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
        return $this->_EditObjectId;
    }

    /**
     * Returns active/editable object id
     *
     * @return string
     */
    public function setEditObjectId( $sEditObjectId )
    {
        $this->_EditObjectId = $sEditObjectId;
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
     *
     * @return null
     */
    public function setItemShopId( $sShopId )
    {
        $this->_sItemShopId = $sShopId;
    }

    public function getConfig()
    {
        return oxRegistry::getConfig();
    }
}