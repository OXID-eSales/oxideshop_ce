<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;
use oxField;

/**
 * Newsletter Subscriptions manager
 * Performs user managing function
 * information, deletion and other.
 *
 */
class NewsSubscribed extends \OxidEsales\Eshop\Core\Model\BaseModel
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

        $this->init('oxnewssubscribed');
    }

    /**
     * Loads object (newssubscription) details from DB. Returns true on success.
     *
     * @param string $oxId oxnewssubscribed ID
     *
     * @return bool
     */
    public function load($oxId)
    {
        $blRet = parent::load($oxId);

        if ($this->oxnewssubscribed__oxdboptin->value == 1) {
            $this->_blWasSubscribed = true;
        } elseif ($this->oxnewssubscribed__oxdboptin->value == 2) {
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
    public function loadFromEmail($sEmailAddress)
    {
        $userOxid = $this->getSubscribedUserIdByEmail($sEmailAddress);
        return $this->load($userOxid);
    }

    /**
     * Get subscribed user id by email.
     *
     * @param string $email
     *
     * @return string
     */
    protected function getSubscribedUserIdByEmail($email)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $params = [
            ':oxemail' => (string) $email
        ];

        $userOxid = $database->getOne("select oxid from oxnewssubscribed 
            where oxemail = :oxemail ", $params);

        return $userOxid;
    }

    /**
     * Loader which loads news subscription according to subscribers oxid
     *
     * @param string $sOxUserId subscribers oxid
     *
     * @return bool
     */
    public function loadFromUserId($sOxUserId)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $params = [
            ':oxuserid' => $sOxUserId,
            ':oxshopid' => $this->getConfig()->getShopId()
        ];

        $sOxId = $oDb->getOne("select oxid from oxnewssubscribed 
            where oxuserid = :oxuserid and oxshopid = :oxshopid", $params);

        return $this->load($sOxId);
    }

    /**
     * Inserts nbews object data to DB. Returns true on success.
     *
     * @return mixed oxid on success or false on failure
     */
    protected function _insert()
    {
        // set subscription date
        $this->oxnewssubscribed__oxsubscribed = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s'), \OxidEsales\Eshop\Core\Field::T_RAW);

        return parent::_insert();
    }

    /**
     * We need to check if we unsubscribe here
     *
     * @return mixed oxid on success or false on failure
     */
    protected function _update()
    {
        if (($this->_blWasSubscribed || $this->_blWasPreSubscribed) && !$this->oxnewssubscribed__oxdboptin->value) {
            // set unsubscription date
            $this->oxnewssubscribed__oxunsubscribed->setValue(date('Y-m-d H:i:s'));
            // 0001974 Same object can be called many times without requiring to renew date.
            // If so happens, it would have _aSkipSaveFields set to skip date field. So need to check and
            // release if _aSkipSaveFields are set for field oxunsubscribed.
            $aSkipSaveFieldsKeys = array_keys($this->_aSkipSaveFields, 'oxunsubscribed');
            foreach ($aSkipSaveFieldsKeys as $iSkipSaveFieldKey) {
                unset($this->_aSkipSaveFields[$iSkipSaveFieldKey]);
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
     */
    public function setOptInStatus($iStatus)
    {
        $this->oxnewssubscribed__oxdboptin = new \OxidEsales\Eshop\Core\Field($iStatus, \OxidEsales\Eshop\Core\Field::T_RAW);
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
     */
    public function setOptInEmailStatus($iStatus)
    {
        $this->oxnewssubscribed__oxemailfailed = new \OxidEsales\Eshop\Core\Field($iStatus, \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->save();
    }

    /**
     * Check if was ever unsubscribed by unsubscribed field.
     *
     * @return bool
     */
    public function wasUnsubscribed()
    {
        if ('0000-00-00 00:00:00' != $this->oxnewssubscribed__oxunsubscribed->value) {
            return true;
        }

        return false;
    }

    /**
     * This method is called from \OxidEsales\Eshop\Application\Model\User::update. Currently it updates user
     * information kept in db
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser subscription user object
     *
     * @return bool
     */
    public function updateSubscription($oUser)
    {
        // user email changed ?
        if ($oUser->oxuser__oxusername->value && $this->oxnewssubscribed__oxemail->value != $oUser->oxuser__oxusername->value) {
            $this->oxnewssubscribed__oxemail = new \OxidEsales\Eshop\Core\Field($oUser->oxuser__oxusername->value, \OxidEsales\Eshop\Core\Field::T_RAW);
        }

        // updating some other fields
        $this->oxnewssubscribed__oxsal = new \OxidEsales\Eshop\Core\Field($oUser->oxuser__oxsal->value, \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->oxnewssubscribed__oxfname = new \OxidEsales\Eshop\Core\Field($oUser->oxuser__oxfname->value, \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->oxnewssubscribed__oxlname = new \OxidEsales\Eshop\Core\Field($oUser->oxuser__oxlname->value, \OxidEsales\Eshop\Core\Field::T_RAW);

        return (bool) $this->save();
    }
}
