<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxDb;

/**
 * Promotion List manager.
 *
 */
class ActionList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxactions';

    /**
     * Loads x last finished promotions
     *
     * @param int $iCount count to load
     */
    public function loadFinishedByCount($iCount)
    {
        $sViewName = $this->getBaseObject()->getViewName();
        $sDate = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select * from {$sViewName} where oxtype=2 and oxactive=1 and oxshopid='" . $this->getConfig()->getShopId() . "' and oxactiveto>0 and oxactiveto < " . $oDb->quote($sDate) . "
               " . $this->_getUserGroupFilter() . "
               order by oxactiveto desc, oxactivefrom desc limit " . (int) $iCount;
        $this->selectString($sQ);
        $this->_aArray = array_reverse($this->_aArray, true);
    }

    /**
     * Loads last finished promotions after given timespan
     *
     * @param int $iTimespan timespan to load
     */
    public function loadFinishedByTimespan($iTimespan)
    {
        $sViewName = $this->getBaseObject()->getViewName();
        $sDateTo = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());
        $sDateFrom = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - $iTimespan);
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select * from {$sViewName} where oxtype=2 and oxactive=1 and oxshopid='" . $this->getConfig()->getShopId() . "' and oxactiveto < " . $oDb->quote($sDateTo) . " and oxactiveto > " . $oDb->quote($sDateFrom) . "
               " . $this->_getUserGroupFilter() . "
               order by oxactiveto, oxactivefrom";
        $this->selectString($sQ);
    }

    /**
     * Loads current promotions
     */
    public function loadCurrent()
    {
        $sViewName = $this->getBaseObject()->getViewName();
        $sDate = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select * from {$sViewName} where oxtype=2 and oxactive=1 and oxshopid='" . $this->getConfig()->getShopId() . "' and (oxactiveto > " . $oDb->quote($sDate) . " or oxactiveto=0) and oxactivefrom != 0 and oxactivefrom < " . $oDb->quote($sDate) . "
               " . $this->_getUserGroupFilter() . "
               order by oxactiveto, oxactivefrom";
        $this->selectString($sQ);
    }

    /**
     * Loads next not yet started promotions by cound
     *
     * @param int $iCount count to load
     */
    public function loadFutureByCount($iCount)
    {
        $sViewName = $this->getBaseObject()->getViewName();
        $sDate = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select * from {$sViewName} where oxtype=2 and oxactive=1 and oxshopid='" . $this->getConfig()->getShopId() . "' and (oxactiveto > " . $oDb->quote($sDate) . " or oxactiveto=0) and oxactivefrom > " . $oDb->quote($sDate) . "
               " . $this->_getUserGroupFilter() . "
               order by oxactiveto, oxactivefrom limit " . (int) $iCount;
        $this->selectString($sQ);
    }

    /**
     * Loads next not yet started promotions before the given timespan
     *
     * @param int $iTimespan timespan to load
     */
    public function loadFutureByTimespan($iTimespan)
    {
        $sViewName = $this->getBaseObject()->getViewName();
        $sDate = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());
        $sDateTo = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + $iTimespan);
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select * from {$sViewName} where oxtype=2 and oxactive=1 and oxshopid='" . $this->getConfig()->getShopId() . "' and (oxactiveto > " . $oDb->quote($sDate) . " or oxactiveto=0) and oxactivefrom > " . $oDb->quote($sDate) . " and oxactivefrom < " . $oDb->quote($sDateTo) . "
               " . $this->_getUserGroupFilter() . "
               order by oxactiveto, oxactivefrom";
        $this->selectString($sQ);
    }

    /**
     * Returns part of user group filter query
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser user object
     *
     * @return string
     */
    protected function _getUserGroupFilter($oUser = null)
    {
        $oUser = ($oUser == null) ? $this->getUser() : $oUser;
        $sTable = getViewName('oxactions');
        $sGroupTable = getViewName('oxgroups');

        $aIds = [];
        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ($oUser && count($aGroupIds = $oUser->getUserGroups())) {
            foreach ($aGroupIds as $oGroup) {
                $aIds[] = $oGroup->getId();
            }
        }

        $sGroupSql = count($aIds) ? "EXISTS(select oxobject2action.oxid from oxobject2action where oxobject2action.oxactionid=$sTable.OXID and oxobject2action.oxclass='oxgroups' and oxobject2action.OXOBJECTID in (" . implode(', ', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aIds)) . ") )" : '0';
        return " and (
            select
                if(EXISTS(select 1 from oxobject2action, $sGroupTable where $sGroupTable.oxid=oxobject2action.oxobjectid and oxobject2action.oxactionid=$sTable.OXID and oxobject2action.oxclass='oxgroups' LIMIT 1),
                    $sGroupSql,
                    1)
            ) ";
    }

    /**
     * return true if there are any active promotions
     *
     * @return boolean
     */
    public function areAnyActivePromotions()
    {
        return (bool) $this->fetchExistsActivePromotion();
    }


    /**
     * Fetch the information, if there is an active promotion.
     *
     * @return string One, if there is an active promotion.
     */
    protected function fetchExistsActivePromotion()
    {
        $query = "select 1 from " . getViewName('oxactions') . " 
            where oxtypex = :oxtype and oxactive = :oxactive and oxshopid = :oxshopid 
            limit 1";

        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($query, [
            ':oxtype' => 2,
            ':oxactive' => 1,
            ':oxshopid' => $this->getConfig()->getShopId()
        ]);
    }

    /**
     * load active shop banner list
     */
    public function loadBanners()
    {
        $oBaseObject = $this->getBaseObject();
        $oViewName = $oBaseObject->getViewName();
        $sQ = "select * from {$oViewName} where oxtype=3 and " . $oBaseObject->getSqlActiveSnippet()
              . " and oxshopid='" . $this->getConfig()->getShopId() . "' " . $this->_getUserGroupFilter()
              . " order by oxsort";
        $this->selectString($sQ);
    }
}
