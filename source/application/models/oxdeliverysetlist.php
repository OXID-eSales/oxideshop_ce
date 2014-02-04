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

/**
 * DeliverySet list manager.
 *
 * @package model
 */
class oxDeliverySetList extends oxList
{
    /**
     * oxDeliverySetList instance
     * @var oxDeliveryList
     */
    private static $_instance = null;

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
     * @var oxUser
     */
    protected $_oUser = null;

    /**
     * Home country info id
     *
     * @var array
     */
    protected $_sHomeCountry = null;

    /**
     * Class constructor, sets callback so that Shopowner is able to
     * add any information to the article.
     *
     * @param string $sObjectsInListName Object in list
     */
    public function __construct( $sObjectsInListName = 'oxdeliveryset')
    {
        $this->setHomeCountry( $this->getConfig()->getConfigParam( 'aHomeCountry' ) );
        parent::__construct( 'oxdeliveryset' );
    }

    /**
     * Home country setter
     *
     * @param string $sHomeCountry home country id
     *
     * @return null
     */
    public function setHomeCountry( $sHomeCountry )
    {
        if ( is_array( $sHomeCountry ) ) {
            $this->_sHomeCountry = current( $sHomeCountry );
        } else {
            $this->_sHomeCountry = $sHomeCountry;
        }
    }

    /**
     * Returns oxDeliverySetList instance
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxDeliverySetList") instead.
     *
     * @return oxDeliverySetList
     */
    static function getInstance()
    {
        return oxRegistry::get("oxDeliverySetList");
    }


    /**
     * Returns active delivery set list
     *
     * Loads all active delivery sets in list. Additionally
     * checks if set has user customized parameters like
     * assigned users, countries or user groups. Performs
     * additional filtering accordint to these parameters
     *
     * @param oxUser $oUser      user object
     * @param string $sCountryId user country id
     *
     * @return array
     */
    protected function _getList( $oUser = null, $sCountryId = null )
    {
        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ( $oUser === null ) {
            $oUser = $this->getUser();
        } else {
            //set user
            $this->setUser( $oUser );
        }

        $sUserId = $oUser ? $oUser->getId() : '';

        if ( $sUserId !== $this->_sUserId || $sCountryId !== $this->_sCountryId) {

            // chooseing delivery country if it is not set yet
            if ( !$sCountryId ) {

                if ( $oUser ) {
                    $sCountryId = $oUser->getActiveCountry();
                } else {
                    $sCountryId = $this->_sHomeCountry;
                }
            }

            $this->selectString( $this->_getFilterSelect( $oUser, $sCountryId ) );
            $this->_sUserId = $sUserId;
            $this->_sCountryId = $sCountryId;
        }

        $this->rewind();

        return $this;
    }


    /**
     * Creates delivery set list filter SQL to load current state delivery set list
     *
     * @param oxUser $oUser      user object
     * @param string $sCountryId user country id
     *
     * @return string
     */
    protected function _getFilterSelect( $oUser, $sCountryId )
    {
        $sTable = getViewName( 'oxdeliveryset' );
        $sQ  = "select $sTable.* from $sTable ";
        $sQ .= "where ".$this->getBaseObject()->getSqlActiveSnippet().' ';

        // defining initial filter parameters
        $sUserId    = null;
        $aGroupIds  = null;

        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ( $oUser ) {

            // user ID
            $sUserId = $oUser->getId();

            // user groups ( maybe would be better to fetch by function oxuser::getUserGroups() ? )
            $aGroupIds = $oUser->getUserGroups();
        }

        $aIds = array();
        if ( count( $aGroupIds ) ) {
            foreach ( $aGroupIds as $oGroup ) {
                $aIds[] = $oGroup->getId();
            }
        }

        $sUserTable    = getViewName( 'oxuser' );
        $sGroupTable   = getViewName( 'oxgroups' );
        $sCountryTable = getViewName( 'oxcountry' );

        $oDb = oxDb::getDb();

        $sCountrySql = $sCountryId?"EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelset' and oxobject2delivery.OXOBJECTID=".$oDb->quote($sCountryId).")":'0';
        $sUserSql    = $sUserId   ?"EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetu' and oxobject2delivery.OXOBJECTID=".$oDb->quote($sUserId).")":'0';
        $sGroupSql   = count( $aIds ) ?"EXISTS(select oxobject2delivery.oxid from oxobject2delivery where oxobject2delivery.oxdeliveryid=$sTable.OXID and oxobject2delivery.oxtype='oxdelsetg' and oxobject2delivery.OXOBJECTID in (".implode(', ', oxDb::getInstance()->quoteArray($aIds) ).") )":'0';

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
     * @param oxUser $oUser      user object
     * @param string $sCountryId user country id
     * @param string $sDelSet    preferred delivery set ID (optional)
     *
     * @return array
     */
    public function getDeliverySetList( $oUser, $sCountryId, $sDelSet = null )
    {
        $this->_getList( $oUser, $sCountryId );

        // if there is allready chosen delivery set we must start checking from it
        $aList = $this->_aArray;
        if ( $sDelSet && isset( $aList[$sDelSet] ) ) {

            //set it as first element
            $oDelSet = $aList[$sDelSet];
            unset( $aList[$sDelSet] );

            $aList = array_merge( array( $sDelSet => $oDelSet ), $aList );
        }
        return $aList;
    }

    /**
     * Loads deliveryset data, checks if it has payments assigned. If active delivery set id
     * is passed - checks if it can be used, if not - takes first ship set id from list which
     * fits. For active ship set collects payment list info. Retuns array containing:
     *   1. all ship sets that has payment (array)
     *   2. active ship set id (string)
     *   3. payment list for active ship set (array)
     *
     * @param string $sShipSet current ship set id (can be null if not set yet)
     * @param oxuser $oUser    active user
     * @param double $oBasket  basket object
     *
     * @return array
     */
    public function getDeliverySetData( $sShipSet, $oUser, $oBasket )
    {
        $sActShipSet = null;
        $aActSets    = array();
        $aActPaymentList = array();

        if (!$oUser) {
            return;
        }

        $this->_getList( $oUser, $oUser->getActiveCountry() );

        // if there are no shipsets we dont need to load payments
        if ( $this->count() ) {

            // one selected ?
            if ( $sShipSet && !isset( $this->_aArray[$sShipSet] ) ) {
                $sShipSet = null;
            }

            $oPayList = oxRegistry::get("oxPaymentList");
            $oDelList = oxRegistry::get("oxDeliveryList");

            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $dBasketPrice = $oBasket->getPriceForPayment() / $oCur->rate;

            // checking if these ship sets available (number of possible payment methods > 0)
            foreach ( $this as $sShipSetId => $oShipSet ) {

                $aPaymentList = $oPayList->getPaymentList( $sShipSetId, $dBasketPrice, $oUser );
                if ( count( $aPaymentList ) ) {

                    // now checking for deliveries
                    if ( $oDelList->hasDeliveries( $oBasket, $oUser, $oUser->getActiveCountry(), $sShipSetId ) ) {
                        $aActSets[$sShipSetId] = $oShipSet;

                        if ( !$sShipSet || ( $sShipSetId == $sShipSet ) ) {
                            $sActShipSet = $sShipSet = $sShipSetId;
                            $aActPaymentList = $aPaymentList;
                            $oShipSet->blSelected = true;
                        }
                    }
                }
            }
        }

        return array( $aActSets, $sActShipSet, $aActPaymentList );
    }

    /**
     * Get current user object. If user is not set, try to get current user.
     *
     * @return oxUser
     */
    public function getUser()
    {
        if ( !$this->_oUser ) {
            $this->_oUser = parent::getUser();
        }

        return $this->_oUser;
    }

    /**
     * Set current user object
     *
     * @param oxUser $oUser user object
     *
     * @return null
     */
    public function setUser( $oUser )
    {
        $this->_oUser = $oUser;
    }

    /**
     * Loads an object including all delivery sets which are not mapped to a
     * predefined GoodRelations delivery method.
     *
     * @return null
     */
    public function loadNonRDFaDeliverySetList()
    {
        $sTable = getViewName( 'oxdeliveryset' );
        $sSubSql = "SELECT * FROM oxobject2delivery WHERE oxobject2delivery.OXDELIVERYID = $sTable.OXID AND oxobject2delivery.OXTYPE = 'rdfadeliveryset'";
        $this->selectString( "SELECT $sTable.* FROM $sTable WHERE NOT EXISTS($sSubSql) AND $sTable.OXACTIVE = 1" );
    }

    /**
     * Loads delivery set mapped to a
     * predefined GoodRelations delivery method.
     *
     * @param string $sDelId delivery set id
     *
     * @return null
     */
    public function loadRDFaDeliverySetList($sDelId = null)
    {
        $sTable = getViewName( 'oxdeliveryset' );
        if ($sDelId) {
            $oDb = oxDb::getDb();
            $sSubSql = "( select $sTable.* from $sTable left join oxdel2delset on oxdel2delset.oxdelsetid=$sTable.oxid where ".$this->getBaseObject()->getSqlActiveSnippet()." and oxdel2delset.oxdelid = ".$oDb->quote($sDelId)." ) as $sTable";
        } else {
            $sSubSql = $sTable;
        }
        $sQ  = "select $sTable.*, oxobject2delivery.oxobjectid from $sSubSql left join (select oxobject2delivery.* from oxobject2delivery where oxobject2delivery.oxtype = 'rdfadeliveryset' ) as oxobject2delivery on oxobject2delivery.oxdeliveryid=$sTable.oxid where ".$this->getBaseObject()->getSqlActiveSnippet()." ";
        $this->selectString( $sQ );
    }

}
