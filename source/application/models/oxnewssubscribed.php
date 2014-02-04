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
 * Newsletter Subscriptions manager
 * Performs user managing function
 * information, deletion and other.
 *
 * @package model
 */
class oxNewsSubscribed extends oxBase
{
    /**
     * Subscription marker
     *
     * @var bool
     */
    protected $_blWasSubscribed = false;

    /**
     * Subscription marker. Marks that newsletter was subscribed but wasn't confirmed.
     *
     * @var bool
     */
    protected $_blWasPreSubscribed = false;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxnewssubscribed';


    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {

        parent::__construct();

        $this->init( 'oxnewssubscribed' );
    }


    /**
     * Loads object (newssubscription) details from DB. Returns true on success.
     *
     * @param string $oxId oxnewssubscribed ID
     *
     * @return bool
     */
    public function load( $oxId )
    {
        $blRet = parent::load( $oxId );

        if ( $this->oxnewssubscribed__oxdboptin->value == 1 ) {
            $this->_blWasSubscribed = true;
        } elseif ( $this->oxnewssubscribed__oxdboptin->value == 2 ) {
            $this->_blWasPreSubscribed = true;
        }

        return $blRet;
    }

    /**
     * Loader which loads news subscription according to subscribers email address
     *
     * @param string $sEmailAddress subscribers email address
     *
     * @return bool
     */
    public function loadFromEmail( $sEmailAddress )
    {
        $oDb = oxDb::getDb();
        $sEmailAddressQuoted = $oDb->quote( $sEmailAddress );
            $sOxId = $oDb->getOne( "select oxid from oxnewssubscribed where oxemail = {$sEmailAddressQuoted} " );

        return $this->load( $sOxId );
    }

    /**
     * Loader which loads news subscription according to subscribers oxid
     *
     * @param string $sOxUserId subscribers oxid
     *
     * @return bool
     */
    public function loadFromUserId( $sOxUserId )
    {
        $oDb = oxDb::getDb();
        $sOxId = $oDb->getOne( "select oxid from oxnewssubscribed where oxuserid = {$oDb->quote( $sOxUserId )} and oxshopid = {$oDb->quote( $this->getConfig()->getShopId() )}" );
        return $this->load( $sOxId );
    }

    /**
     * Inserts nbews object data to DB. Returns true on success.
     *
     * @return mixed oxid on success or false on failure
     */
    protected function _insert()
    {
        // set subscription date
        $this->oxnewssubscribed__oxsubscribed = new oxField(date( 'Y-m-d H:i:s' ), oxField::T_RAW);
        return parent::_insert();
    }

    /**
     * We need to check if we unsubscribe here
     *
     * @return mixed oxid on success or false on failure
     */
    protected function _update()
    {
        if ( ( $this->_blWasSubscribed || $this->_blWasPreSubscribed ) && !$this->oxnewssubscribed__oxdboptin->value ) {
            // set unsubscription date
            $this->oxnewssubscribed__oxunsubscribed->setValue(date( 'Y-m-d H:i:s' ));
            // 0001974 Same object can be called many times without requiring to renew date.
            // If so happens, it would have _aSkipSaveFields set to skip date field. So need to check and
            // release if _aSkipSaveFields are set for field oxunsubscribed.
            $aSkipSaveFieldsKeys = array_keys( $this->_aSkipSaveFields, 'oxunsubscribed' );
            foreach ( $aSkipSaveFieldsKeys as $iSkipSaveFieldKey ) {
                unset ( $this->_aSkipSaveFields[ $iSkipSaveFieldKey ] );
            }
        } else {
            // don't update date
            $this->_aSkipSaveFields[] = 'oxunsubscribed';
        }

        return parent::_update();
    }

    /**
     * Newsletter subscription status getter
     *
     * @return int
     */
    public function getOptInStatus()
    {
        return $this->oxnewssubscribed__oxdboptin->value;
    }

    /**
     * Newsletter subscription status setter
     *
     * @param int $iStatus subscription status
     *
     * @return null
     */
    public function setOptInStatus( $iStatus )
    {
        $this->oxnewssubscribed__oxdboptin = new oxField($iStatus, oxField::T_RAW);
        $this->save();
    }

    /**
     * Newsletter subscription email sending status getter
     *
     * @return int
     */
    public function getOptInEmailStatus()
    {
        return $this->oxnewssubscribed__oxemailfailed->value;
    }

    /**
     * Newsletter subscription email sending status setter
     *
     * @param int $iStatus subscription status
     *
     * @return null
     */
    public function setOptInEmailStatus( $iStatus )
    {
        $this->oxnewssubscribed__oxemailfailed = new oxField($iStatus, oxField::T_RAW);
        $this->save();
    }

    /**
     * Check if was ever unsubscribed by unsubscribed field.
     *
     * @return bool
     */
    public function wasUnsubscribed()
    {
        if ( '0000-00-00 00:00:00' != $this->oxnewssubscribed__oxunsubscribed ) {
            return true;
        }
        return false;
    }

    /**
     * This method is called from oxuser::update. Currently it updates user
     * information kept in db
     *
     * @param oxuser $oUser subscription user object
     *
     * @return bool
     */
    public function updateSubscription( $oUser )
    {
        // user email changed ?
        if ( $oUser->oxuser__oxusername->value && $this->oxnewssubscribed__oxemail->value != $oUser->oxuser__oxusername->value ) {
            $this->oxnewssubscribed__oxemail = new oxField( $oUser->oxuser__oxusername->value, oxField::T_RAW );
        }

        // updating some other fields
        $this->oxnewssubscribed__oxsal   = new oxField( $oUser->oxuser__oxsal->value, oxField::T_RAW );
        $this->oxnewssubscribed__oxfname = new oxField( $oUser->oxuser__oxfname->value, oxField::T_RAW );
        $this->oxnewssubscribed__oxlname = new oxField( $oUser->oxuser__oxlname->value, oxField::T_RAW );

        return (bool) $this->save();
    }
}
