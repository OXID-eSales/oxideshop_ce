<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxDb;

/**
 * DeliverySet list manager.
 *
 */
class DeliverySetList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Session user Id
     *
     * @var string
     */
    protected $_sUserId = null;

    /**
     * Country Id
     *
     * @var string
     */
    protected $_sCountryId = null;

    /**
     * User object
     *
     * @var \OxidEsales\Eshop\Application\Model\User
     */
    protected $_oUser = null;

    /**
     * Home country info id
     *
     * @var array
     */
    protected $_sHomeCountry = null;

    /**
     * Calls parent constructor and sets home country
     */
    public function __construct()
    {
        $this->setHomeCountry($this->getConfig()->getConfigParam('aHomeCountry'));
        parent::__construct('oxdeliveryset');
    }

    /**
     * Home country setter
     *
     * @param string $sHomeCountry home country id
     */
    public function setHomeCountry($sHomeCountry)
    {
        if (is_array($sHomeCountry)) {
            $this->_sHomeCountry = current($sHomeCountry);
        } else {
            $this->_sHomeCountry = $sHomeCountry;
        }
    }

    /**
     * Returns active delivery set list
     *
     * Loads all active delivery sets in list. Additionally
     * checks if set has user customized parameters like
     * assigned users, countries or user groups. Performs
     * additional filtering according to these parameters
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser      user object
     * @param string                                   $sCountryId user country id
     *
     * @return array
     */
    protected function _getList($oUser = null, $sCountryId = null)
    {
        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ($oUser === null) {
            $oUser = $this->getUser();
        } else {
            //set user
            $this->setUser($oUser);
        }

        $sUserId = $oUser ? $oUser->getId() : '';

        if ($sUserId !== $this->_sUserId || $sCountryId !== $this->_sCountryId) {
            // choosing delivery country if it is not set yet
            if (!$sCountryId) {
                if ($oUser) {
                    $sCountryId = $oUser->getActiveCountry();
                } else {
                    $sCountryId = $this->_sHomeCountry;
                }
            }

            $this->selectString($this->_getFilterSelect($oUser, $sCountryId));
            $this->_sUserId = $sUserId;
            $this->_sCountryId = $sCountryId;
        }

        $this->rewind();

        return $this;
    }


    /**
     * Creates delivery set list filter SQL to load current state delivery set list
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser      user object
     * @param string                                   $sCountryId user country id
     *
     * @return string
     */
    protected function _getFilterSelect($oUser, $sCountryId)
    {
        $sTable = getViewName('oxdeliveryset');
        $sQ = "select $sTable.* from $sTable ";
        $sQ .= "where " . $this->getBaseObject()->getSqlActiveSnippet() . ' ';

        // defining initial filter parameters
        $sUserId = null;
        $aGroupIds = [];

        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ($oUser) {
            // user ID
            $sUserId = $oUser->getId();

            // user groups ( maybe would be better to fetch by function \OxidEsales\Eshop\Application\Model\User::getUserGroups() ? )
            $aGroupIds = $oUser->getUserGroups();
        }

        $aIds = [];
        if (count($aGroupIds)) {
            foreach ($aGroupIds as $oGroup) {
                $aIds[] = $oGroup->getId();
            }
        }

        $sUserTable = getViewName('oxuser');
        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sCountrySql = $sCountryId ? "EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelset' and oxobject2delivery.OXOBJECTID=" . $oDb->quote($sCountryId) . ")" : '0';
        $sUserSql = $sUserId ? "EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetu' and oxobject2delivery.OXOBJECTID=" . $oDb->quote($sUserId) . ")" : '0';
        $sGroupSql = count($aIds) ? "EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetg' and oxobject2delivery.OXOBJECTID in (" . implode(', ', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aIds)) . ") )" : '0';

        $sQ .= "and (
            select
                if(EXISTS(select 1 from oxobject2delivery, $sCountryTable where $sCountryTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelset' LIMIT 1),
                    $sCountrySql,
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sUserTable where $sUserTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetu' LIMIT 1),
                    $sUserSql,
                    1) &&
                if(EXISTS(select 1 from oxobject2delivery, $sGroupTable where $sGroupTable.oxid=oxobject2delivery.oxobjectid and oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetg' LIMIT 1),
                    $sGroupSql,
                    1)
            )";

        //order by
        $sQ .= " order by $sTable.oxpos";

        return $sQ;
    }

    /**
     * Creates current state delivery set list
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser      user object
     * @param string                                   $sCountryId user country id
     * @param string                                   $sDelSet    preferred delivery set ID (optional)
     *
     * @return array
     */
    public function getDeliverySetList($oUser, $sCountryId, $sDelSet = null)
    {
        $this->_getList($oUser, $sCountryId);

        // if there is already chosen delivery set we must start checking from it
        $aList = $this->_aArray;
        if ($sDelSet && isset($aList[$sDelSet])) {
            //set it as first element
            $oDelSet = $aList[$sDelSet];
            unset($aList[$sDelSet]);

            $aList = array_merge([$sDelSet => $oDelSet], $aList);
        }

        return $aList;
    }

    /**
     * Loads delivery set data, checks if it has payments assigned. If active delivery set id
     * is passed - checks if it can be used, if not - takes first ship set id from list which
     * fits. For active ship set collects payment list info. Returns array containing:
     *   1. all ship sets that has payment (array)
     *   2. active ship set id (string)
     *   3. payment list for active ship set (array)
     *
     * @param string                                   $sShipSet current ship set id (can be null if not set yet)
     * @param \OxidEsales\Eshop\Application\Model\User $oUser    active user
     * @param double                                   $oBasket  basket object
     *
     * @return array
     */
    public function getDeliverySetData($sShipSet, $oUser, $oBasket)
    {
        $sActShipSet = null;
        $aActSets = [];
        $aActPaymentList = [];

        if (!$oUser) {
            return;
        }

        $this->_getList($oUser, $oUser->getActiveCountry());

        // if there are no shipping sets we don't need to load payments
        if ($this->count()) {
            // one selected ?
            if ($sShipSet && !isset($this->_aArray[$sShipSet])) {
                $sShipSet = null;
            }

            $oPayList = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\PaymentList::class);
            $oDelList = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\DeliveryList::class);

            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $dBasketPrice = $oBasket->getPriceForPayment() / $oCur->rate;

            // checking if these ship sets available (number of possible payment methods > 0)
            foreach ($this as $sShipSetId => $oShipSet) {
                $aPaymentList = $oPayList->getPaymentList($sShipSetId, $dBasketPrice, $oUser);
                if (count($aPaymentList)) {
                    // now checking for deliveries
                    if ($oDelList->hasDeliveries($oBasket, $oUser, $oUser->getActiveCountry(), $sShipSetId)) {
                        $aActSets[$sShipSetId] = $oShipSet;

                        if (!$sShipSet || ($sShipSetId == $sShipSet)) {
                            $sActShipSet = $sShipSet = $sShipSetId;
                            $aActPaymentList = $aPaymentList;
                            $oShipSet->blSelected = true;
                        }
                    }
                }
            }
        }

        return [$aActSets, $sActShipSet, $aActPaymentList];
    }

    /**
     * Get current user object. If user is not set, try to get current user.
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    public function getUser()
    {
        if (!$this->_oUser) {
            $this->_oUser = parent::getUser();
        }

        return $this->_oUser;
    }

    /**
     * Set current user object
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser user object
     */
    public function setUser($oUser)
    {
        $this->_oUser = $oUser;
    }

    /**
     * Loads an object including all delivery sets which are not mapped to a
     * predefined GoodRelations delivery method.
     */
    public function loadNonRDFaDeliverySetList()
    {
        $sTable = getViewName('oxdeliveryset');
        $sSubSql = "SELECT * FROM oxobject2delivery WHERE oxobject2delivery.OXDELIVERYID = $sTable.OXID AND oxobject2delivery.OXTYPE = 'rdfadeliveryset'";
        $this->selectString("SELECT $sTable.* FROM $sTable WHERE NOT EXISTS($sSubSql) AND $sTable.OXACTIVE = 1");
    }

    /**
     * Loads delivery set mapped to a
     * predefined GoodRelations delivery method.
     *
     * @param string $sDelId delivery set id
     */
    public function loadRDFaDeliverySetList($sDelId = null)
    {
        $sTable = getViewName('oxdeliveryset');
        if ($sDelId) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sSubSql = "( select $sTable.* from $sTable left join oxdel2delset on oxdel2delset.oxdelsetid=$sTable.oxid where " . $this->getBaseObject()->getSqlActiveSnippet() . " and oxdel2delset.oxdelid = :oxdelid ) as $sTable";
        } else {
            $sSubSql = $sTable;
        }
        $sQ = "select $sTable.*, oxobject2delivery.oxobjectid from $sSubSql left join (select oxobject2delivery.* from oxobject2delivery where oxobject2delivery.oxtype = 'rdfadeliveryset' ) as oxobject2delivery on oxobject2delivery.oxdeliveryid=$sTable.oxid where " . $this->getBaseObject()->getSqlActiveSnippet() . " ";
        $this->selectString($sQ, [
            ':oxdelid' => $sDelId
        ]);
    }
}
