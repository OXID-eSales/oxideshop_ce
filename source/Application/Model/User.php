<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\Exception\CookieException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\RandomTokenGeneratorBridgeInterface;

/**
 * User manager.
 * Performs user managing function, as assigning to groups, updating
 * information, deletion and other.
 */
class User extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    const USER_COOKIE_SALT = 'user_cookie_salt';

    /**
     * Shop control variable
     *
     * @var string
     */
    protected $_blDisableShopCheck = true;

    /**
     * Current Subscription Object if there is any
     *
     * @var object
     */
    protected $_oNewsSubscription = null;

    /**
     * Current object class name
     *
     * @var string
     */
    protected $_sClassName = 'oxuser';

    /**
     * User wish / notice list
     *
     * @var array
     */
    protected $_aBaskets = [];

    /**
     * User groups list
     *
     * @var ListModel
     */
    protected $_oGroups;

    /**
     * User address list array
     *
     * @var array
     */
    protected $_aAddresses = [];

    /**
     * User payment list
     *
     * @var ListModel
     */
    protected $_oPayments;

    /**
     * User recommendation list
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var ListModel
     */
    protected $_oRecommList;

    /**
     * Mall user status
     *
     * @var bool
     */
    protected $_blMallUsers = false;

    /**
     * user cookies
     *
     * @var array
     */
    protected static $_aUserCookie = [];

    /**
     * Notice list item's count
     *
     * @var integer
     */
    protected $_iCntNoticeListArticles = null;

    /**
     * Wishlist item's count
     *
     * @var integer
     */
    protected $_iCntWishListArticles = null;

    /**
     * User recommlist count
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var integer
     */
    protected $_iCntRecommLists = null;

    /**
     * Password update key
     *
     * @var string
     */
    protected $_sUpdateKey = null;

    /**
     * User loaded from cookie
     *
     * @var bool
     */
    protected $_blLoadedFromCookie = null;

    /**
     * User selected shipping address id
     *
     * @var string
     */
    protected $_sSelAddressId = null;

    /**
     * User selected shipping address
     *
     * @var object
     */
    protected $_oSelAddress = null;

    /**
     * Id of wishlist user
     *
     * @var string
     */
    protected $_sWishId = null;

    /**
     * Country title field
     *
     * @var object
     */
    protected $_oUserCountryTitle = null;

    /**
     * @var \OxidEsales\Eshop\Application\Model\State
     */
    protected $_oStateObject = null;

    /**
     * @var bool
     *
     * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface`
     *                                        was added as the new default for hashing passwords.
     */
    private $isOutdatedPasswordHashAlgorithmUsed = false;

    /**
     * Gets state object.
     *
     * @return \OxidEsales\Eshop\Application\Model\State
     */
    protected function getStateObject()
    {
        if (is_null($this->_oStateObject)) {
            $this->_oStateObject = oxNew(\OxidEsales\Eshop\Application\Model\State::class);
        }

        return $this->_oStateObject;
    }

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        $this->setMallUsersStatus(Registry::getConfig()->getConfigParam('blMallUsers'));

        parent::__construct();
        $this->init('oxuser');
    }

    /**
     * Sets mall user status
     *
     * @param bool $blOn mall users is on or off
     */
    public function setMallUsersStatus($blOn = false)
    {
        $this->_blMallUsers = $blOn;
    }

    /**
     * Getter for special not frequently used fields
     *
     * @param string $sParamName name of parameter to get value
     *
     * @return mixed
     */
    public function __get($sParamName)
    {
        // it saves memory using - loads data only if it is used
        switch ($sParamName) {
            case 'oGroups':
                return $this->_oGroups = $this->getUserGroups();
                break;
            case 'iCntNoticeListArticles':
                return $this->_iCntNoticeListArticles = $this->getNoticeListArtCnt();
                break;
            case 'iCntWishListArticles':
                return $this->_iCntWishListArticles = $this->getWishListArtCnt();
                break;
            // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
            case 'iCntRecommLists':
                return $this->_iCntRecommLists = $this->getRecommListsCount();
                break;
            // END deprecated
            case 'oAddresses':
                return $this->getUserAddresses();
                break;
            case 'oPayments':
                return $this->_oPayments = $this->getUserPayments();
                break;
            case 'oxuser__oxcountry':
                return $this->oxuser__oxcountry = $this->getUserCountry();
                break;
            case 'sDBOptin':
                return $this->sDBOptin = $this->getNewsSubscription()->getOptInStatus();
                break;
            case 'sEmailFailed':
                return $this->sEmailFailed = $this->getNewsSubscription()->getOptInEmailStatus();
                break;
        }
    }

    /**
     * Returns user newsletter subscription controller object
     *
     * @return object oxnewssubscribed
     */
    public function getNewsSubscription()
    {
        if ($this->_oNewsSubscription !== null) {
            return $this->_oNewsSubscription;
        }

        $this->_oNewsSubscription = oxNew(\OxidEsales\Eshop\Application\Model\NewsSubscribed::class);

        // if subscription object is not set yet - we should create one
        if (!$this->_oNewsSubscription->loadFromUserId($this->getId())) {
            if (!$this->_oNewsSubscription->loadFromEmail($this->getFieldData('oxusername'))) {
                // no subscription defined yet - creating one

                $this->_oNewsSubscription->assign([
                    'oxuserid' => $this->getId(),
                    'oxemail' => $this->getFieldData('oxusername'),
                    'oxsal' => $this->getFieldData('oxsal'),
                    'oxfname' => $this->getFieldData('oxfname'),
                    'oxlname' => $this->getFieldData('oxlname')
                ]);
            }
        }

        return $this->_oNewsSubscription;
    }

    /**
     * Returns user country (object) according to passed parameters or they
     * are taken from user object ( oxid, country id) and session (language)
     *
     * @param string $sCountryId country id (optional)
     * @param int    $iLang      active language (optional)
     *
     * @return string
     */
    public function getUserCountry($sCountryId = null, $iLang = null)
    {
        if ($this->_oUserCountryTitle == null || $sCountryId) {
            $sId = $sCountryId ? $sCountryId : $this->oxuser__oxcountryid->value;
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $sViewName = $tableViewNameGenerator->getViewName('oxcountry', $iLang);

            $countryTitle = $oDb->getOne("select oxtitle from {$sViewName} where oxid = :oxid", [
                ':oxid' => $sId
            ]);

            $oCountry = new \OxidEsales\Eshop\Core\Field($countryTitle, \OxidEsales\Eshop\Core\Field::T_RAW);
            if (!$sCountryId) {
                $this->_oUserCountryTitle = $oCountry;
            }
        } else {
            return $this->_oUserCountryTitle;
        }

        return $oCountry;
    }

    /**
     * Returns user countryid according to passed name
     *
     * @param string $sCountry country
     *
     * @return string
     */
    public function getUserCountryId($sCountry = null)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sQ = "select oxid from " . $tableViewNameGenerator->getViewName("oxcountry") . "
            where oxactive = '1' and oxisoalpha2 = :oxisoalpha2";
        $sCountryId = $oDb->getOne($sQ, [
            ':oxisoalpha2' => $sCountry
        ]);

        return $sCountryId;
    }

    /**
     * Returns assigned user groups list object
     *
     * @param string $sOXID object ID (default is null)
     *
     * @return object
     */
    public function getUserGroups($sOXID = null)
    {
        if (isset($this->_oGroups)) {
            return $this->_oGroups;
        }

        if (!$sOXID) {
            $sOXID = $this->getId();
        }

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sViewName = $tableViewNameGenerator->getViewName("oxgroups");
        $this->_oGroups = oxNew(ListModel::class, 'oxgroups');
        $sSelect = "select {$sViewName}.* from {$sViewName} left join oxobject2group on oxobject2group.oxgroupsid = {$sViewName}.oxid
                     where oxobject2group.oxobjectid = :oxobjectid";
        $this->_oGroups->selectString($sSelect, [
            ':oxobjectid' => $sOXID
        ]);

        return $this->_oGroups;
    }

    /**
     * Returns user defined Address list object
     *
     * @param string $sUserId object ID (default is null)
     *
     * @return array
     */
    public function getUserAddresses($sUserId = null)
    {
        $sUserId = isset($sUserId) ? $sUserId : $this->getId();
        if (!isset($this->_aAddresses[$sUserId])) {
            $oUserAddressList = oxNew(\OxidEsales\Eshop\Application\Model\UserAddressList::class);
            $oUserAddressList->load($sUserId);
            $this->_aAddresses[$sUserId] = $oUserAddressList;

            // marking selected
            if ($sAddressId = $this->getSelectedAddressId()) {
                foreach ($this->_aAddresses[$sUserId] as $oAddress) {
                    if ($oAddress->getId() === $sAddressId) {
                        $oAddress->setSelected();
                        break;
                    }
                }
            }
        }

        return $this->_aAddresses[$sUserId];
    }

    /**
     * Selected user address setter
     *
     * @param string $sAddressId selected address id
     */
    public function setSelectedAddressId($sAddressId)
    {
        $this->_sSelAddressId = $sAddressId;
    }

    /**
     * Returns user chosen address id ("oxaddressid" or "deladrid")
     *
     * @return string
     */
    public function getSelectedAddressId()
    {
        if ($this->_sSelAddressId !== null) {
            return $this->_sSelAddressId;
        }

        $sAddressId = Registry::getRequest()->getRequestEscapedParameter("oxaddressid");
        if (!$sAddressId && !Registry::getRequest()->getRequestEscapedParameter('reloadaddress')) {
            $sAddressId = Registry::getSession()->getVariable("deladrid");
        }

        return $sAddressId;
    }

    /**
     * Checks if product from wishlist is added
     *
     * @return $sWishId
     */
    protected function getWishListId()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        $this->_sWishId = null;
        // check if we have to set it here
        $oBasket = $session->getBasket();
        foreach ($oBasket->getContents() as $oBasketItem) {
            if ($this->_sWishId = $oBasketItem->getWishId()) {
                // stop on first found
                break;
            }
        }

        return $this->_sWishId;
    }

    /**
     * Sets in the array \OxidEsales\Eshop\Application\Model\User::_aAddresses selected address.
     * Returns user selected address object.
     *
     * @return object $oSelectedAddress
     */
    public function getSelectedAddress()
    {
        if ($this->_oSelAddress !== null) {
            return $this->_oSelAddress;
        }

        $oSelectedAddress = null;
        $oAddresses = $this->getUserAddresses();
        if ($oAddresses->count()) {
            if ($sAddressId = $this->getSelectedAddressId()) {
                foreach ($oAddresses as $oAddress) {
                    if ($oAddress->getId() == $sAddressId) {
                        $oAddress->selected = 1;
                        $oAddress->setSelected();
                        $oSelectedAddress = $oAddress;
                        break;
                    }
                }
            }

            // in case none is set - setting first one
            if (!$oSelectedAddress) {
                if (!$sAddressId || $sAddressId >= 0) {
                    $oAddresses->rewind();
                    $oAddress = $oAddresses->current();
                } else {
                    $aAddresses = $oAddresses->getArray();
                    $oAddress = array_pop($aAddresses);
                }
                $oAddress->selected = 1;
                $oAddress->setSelected();
                $oSelectedAddress = $oAddress;
            }
        }
        $this->_oSelAddress = $oSelectedAddress;

        return $oSelectedAddress;
    }

    /**
     * Returns user payment history list object
     *
     * @param string $sOXID object ID (default is null)
     *
     * @return ListModel with oxuserpayments objects
     */
    public function getUserPayments($sOXID = null)
    {
        if ($this->_oPayments === null) {
            if (!$sOXID) {
                $sOXID = $this->getId();
            }

            $sSelect = 'select * from oxuserpayments
                where oxuserid = :oxuserid ';

            $this->_oPayments = oxNew(ListModel::class);
            $this->_oPayments->init('oxUserPayment');
            $this->_oPayments->selectString($sSelect, [
                ':oxuserid' => $sOXID
            ]);
        }

        return $this->_oPayments;
    }

    /**
     * Saves (updates) user object data information in DB. Return true on success.
     *
     * @return bool
     */
    public function save()
    {
        $blAddRemark = false;
        if (
            $this->getFieldData('oxpassword')
            && (!$this->oxuser__oxregister instanceof \OxidEsales\Eshop\Core\Field || $this->oxuser__oxregister->value < 1)
        ) {
            $blAddRemark = true;
            //save oxregister value
            $this->oxuser__oxregister = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s'), \OxidEsales\Eshop\Core\Field::T_RAW);
        }

        // setting user rights
        $this->oxuser__oxrights = new \OxidEsales\Eshop\Core\Field(
            $this->getUserRights(),
            \OxidEsales\Eshop\Core\Field::T_RAW
        );

        // processing birth date which came from output as array
        if ($this->oxuser__oxbirthdate && is_array($this->oxuser__oxbirthdate->value)) {
            $this->oxuser__oxbirthdate = new \OxidEsales\Eshop\Core\Field(
                $this->convertBirthday($this->oxuser__oxbirthdate->value),
                \OxidEsales\Eshop\Core\Field::T_RAW
            );
        }

        $blRet = parent::save();

        //add registered remark
        if ($blAddRemark && $blRet) {
            $oRemark = oxNew(\OxidEsales\Eshop\Application\Model\Remark::class);
            $oRemark->oxremark__oxtext = new \OxidEsales\Eshop\Core\Field(
                Registry::getLang()->translateString('usrRegistered', null, true),
                \OxidEsales\Eshop\Core\Field::T_RAW
            );
            $oRemark->oxremark__oxtype = new \OxidEsales\Eshop\Core\Field('r', \OxidEsales\Eshop\Core\Field::T_RAW);
            $oRemark->oxremark__oxparentid = new \OxidEsales\Eshop\Core\Field($this->getId(), \OxidEsales\Eshop\Core\Field::T_RAW);
            $oRemark->save();
        }

        return $blRet;
    }

    /**
     * Overrides parent isDerived check and returns true
     *
     * @return bool
     */
    public function allowDerivedUpdate()
    {
        return true;
    }

    /**
     * Checks if this object is in group, returns true on success.
     *
     * @param string $sGroupID user group ID
     *
     * @return bool
     */
    public function inGroup($sGroupID)
    {
        $blIn = false;
        if (($oGroups = $this->getUserGroups())) {
            $blIn = isset($oGroups[$sGroupID]);
        }

        return $blIn;
    }

    /**
     * Removes user data stored in some DB tables (such as oxuserpayments, oxaddress
     * oxobject2group, oxremark, etc). Return true on success.
     *
     * @param string $oxid object ID (default null)
     *
     * @return bool
     */
    public function delete($oxid = null)
    {
        if (!$oxid) {
            $oxid = $this->getId();
        }
        if (!$oxid) {
            return false;
        }

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $database->startTransaction();
        try {
            if (parent::delete($oxid)) {
                $quotedUserId = $database->quote($oxid);

                $this->deleteAddresses($database);
                $this->deleteUserFromGroups($database);
                $this->deleteBaskets($database);
                $this->deleteNewsletterSubscriptions($database);
                $this->deleteDeliveries($database);
                $this->deleteDiscounts($database);
                $this->deleteRecommendationLists($database);
                $this->deleteReviews($database);
                $this->deleteRatings($database);
                $this->deletePriceAlarms($database);
                $this->deleteAcceptedTerms($database);
                $this->deleteNotOrderRelatedRemarks($database);

                $this->deleteAdditionally($quotedUserId);
            }

            $database->commitTransaction();
            $deleted = true;
        } catch (\Exception $exeption) {
            $database->rollbackTransaction();

            throw $exeption;
        }

        return $deleted;
    }

    /**
     * Loads object (user) details from DB. Returns true on success.
     *
     * @param string $oxID User ID
     *
     * @return bool
     */
    public function load($oxID)
    {
        $blRet = parent::load($oxID);

        // convert date's to international format
        if (isset($this->oxuser__oxcreate->value)) {
            $this->oxuser__oxcreate->setValue(Registry::getUtilsDate()->formatDBDate($this->oxuser__oxcreate->value));
        }

        // change newsSubcription user id
        if (isset($this->_oNewsSubscription)) {
            $this->_oNewsSubscription->oxnewssubscribed__oxuserid = new \OxidEsales\Eshop\Core\Field($oxID, \OxidEsales\Eshop\Core\Field::T_RAW);
        }

        return $blRet;
    }

    /**
     * Checks if user exists in database.
     *
     * @param string $sOXID object ID (default null)
     *
     * @return bool
     */
    public function exists($sOXID = null)
    {
        if (!$sOXID) {
            $sOXID = $this->getId();
        }
        //#5901 if physical record exists return true unconditionally
        if (parent::exists($sOXID)) {
            $this->setId($sOXID);
            return true;
        }

        //additional username check
        //This part is used by not yet saved user object, to detect the case when such username exists in db.
        //Basically it is called when anonymous visitor enters existing username for newsletter subscription
        //see Newsletter::send()
        //TODO: transfer this validation to newsletter part

        $params = [];

        $sShopSelect = '';
        if (!$this->_blMallUsers && $this->getFieldData('oxrights') != 'malladmin') {
            $sShopSelect = ' AND oxshopid = :oxshopid ';
            $params[':oxshopid'] = Registry::getConfig()->getShopId();
        }

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $sSelect = 'SELECT oxid FROM ' . $this->getViewName() . '
                    WHERE oxusername = :oxusername ';
        $sSelect .= $sShopSelect;
        $params[':oxusername'] = (string) $this->getFieldData('oxusername');

        if (($sOxid = $masterDb->getOne($sSelect, $params))) {
            // update - set oxid
            $this->setId($sOxid);

            return true;
        }

        return false;
    }

    /**
     * Returns object with ordering information (order articles list).
     *
     * @param int $iLimit how many entries to load
     * @param int $iPage  which page to start
     *
     * @return ListModel
     */
    public function getOrders($iLimit = false, $iPage = 0)
    {
        $oOrders = oxNew(ListModel::class);
        $oOrders->init('oxorder');

        if ($iLimit !== false) {
            $oOrders->setSqlLimit($iLimit * $iPage, $iLimit);
        }

        //P
        // Lists does not support loading from two tables, so orders
        // articles now are loaded in account_order.php view and no need to use blLoadProdInfo
        // forcing to load product info which is used in templates
        // $oOrders->aSetBeforeAssign['blLoadProdInfo'] = true;

        //loading order for registered user
        if ($this->oxuser__oxregister->value > 1) {
            $sQ = 'select * from oxorder
                where oxuserid = :oxuserid
                and oxorderdate >= :oxorderdate ';
            $sQ = $this->updateGetOrdersQuery($sQ);

            $sQ .= ' order by oxorderdate desc ';
            $oOrders->selectString($sQ, [
                ':oxuserid' => $this->getId(),
                ':oxorderdate' => $this->oxuser__oxregister->value
            ]);
        }

        return $oOrders;
    }

    /**
     * Caclulates amount of orders made by user
     *
     * @return int
     */
    public function getOrderCount()
    {
        $iCnt = 0;
        if ($this->getId() && $this->oxuser__oxregister->value > 1) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = 'select count(*) from oxorder
                where oxuserid = :oxuserid
                    AND oxorderdate >= :oxorderdate
                    and oxshopid = :oxshopid ';
            $iCnt = (int) $oDb->getOne($sQ, [
                ':oxuserid' => $this->getId(),
                ':oxorderdate' => $this->oxuser__oxregister->value,
                ':oxshopid' => Registry::getConfig()->getShopId()
            ]);
        }

        return $iCnt;
    }

    /**
     * Returns amount of articles in noticelist
     *
     * @return int
     */
    public function getNoticeListArtCnt()
    {
        if ($this->_iCntNoticeListArticles === null) {
            $this->_iCntNoticeListArticles = 0;
            if ($this->getId()) {
                $this->_iCntNoticeListArticles = $this->getBasket('noticelist')->getItemCount();
            }
        }

        return $this->_iCntNoticeListArticles;
    }

    /**
     * Calculating user wishlist item count
     *
     * @return int
     */
    public function getWishListArtCnt()
    {
        if ($this->_iCntWishListArticles === null) {
            $this->_iCntWishListArticles = false;
            if ($this->getId()) {
                $this->_iCntWishListArticles = $this->getBasket('wishlist')->getItemCount();
            }
        }

        return $this->_iCntWishListArticles;
    }

    /**
     * Returns encoded delivery address.
     *
     * @return string
     */
    public function getEncodedDeliveryAddress()
    {
        return md5($this->getMergedAddressFields());
    }

    /**
     * Returns user country ID, but If delivery address is given - returns
     * delivery country.
     *
     * @return string
     */
    public function getActiveCountry()
    {
        $sDeliveryCountry = '';
        $soxAddressId = Registry::getSession()->getVariable('deladrid');
        if ($soxAddressId) {
            $oDelAddress = oxNew(\OxidEsales\Eshop\Application\Model\Address::class);
            $oDelAddress->load($soxAddressId);
            $sDeliveryCountry = $oDelAddress->oxaddress__oxcountryid->value;
        } elseif ($this->getId()) {
            $sDeliveryCountry = $this->oxuser__oxcountryid->value;
        } else {
            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            if ($oUser->loadActiveUser()) {
                $sDeliveryCountry = $oUser->oxuser__oxcountryid->value;
            }
        }

        return $sDeliveryCountry;
    }

    /**
     * Inserts new or updates existing user
     *
     * @throws UserException exception
     *
     * @return bool
     */
    public function createUser()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sShopID = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId();

        // check if user exists AND there is no password - in this case we update otherwise we try to insert
        $sSelect = "select oxid from oxuser
            where oxusername = :oxusername
            and oxpassword = :oxpassword ";
        $params = [
            ':oxusername' => (string) $this->oxuser__oxusername->value,
            ':oxpassword' => ''
        ];
        if (!$this->_blMallUsers) {
            $sSelect .= " and oxshopid = :oxshopid ";
            $params[':oxshopid'] = $sShopID;
        }
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $oldUserId = $masterDb->getOne($sSelect, $params);

        if ($oldUserId) {
            $this->delete($oldUserId);
        } elseif ($this->_blMallUsers) {
            // must be sure if there is no duplicate user
            $sQ = "select oxid from oxuser
                where oxusername = :oxusername
                and oxusername != '' ";
            $params = [
                ':oxusername' => (string) $this->oxuser__oxusername->value
            ];

            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
            if ($masterDb->getOne($sQ, $params)) {
                /** @var UserException $oEx */
                $oEx = oxNew(UserException::class);
                $oLang = Registry::getLang();
                $oEx->setMessage(sprintf($oLang->translateString('ERROR_MESSAGE_USER_USEREXISTS', $oLang->getTplLanguage()), $this->oxuser__oxusername->value));
                throw $oEx;
            }
        }

        $this->oxuser__oxshopid = new Field($sShopID, Field::T_RAW);

        $newUserId = $this->save();
        if ($newUserId === false) {
            throw oxNew(UserException::class, 'ERROR_MESSAGE_USER_USERCREATIONFAILED');
        } else {
            // @TODO the following statements make no sense and should be removed: oxuser__oxid is freshly created and the conditions will never match
            // dropping/cleaning old delivery address/payment info
            $oDb->execute("delete from oxaddress where oxaddress.oxuserid = :oxuserid", [
                ':oxuserid' => $this->oxuser__oxid->value
            ]);

            $query = "update oxuserpayments
                      set oxuserpayments.oxuserid = :newUserId
                      where oxuserpayments.oxuserid = :oldUserId";
            $oDb->execute($query, [
                ':newUserId' => $this->oxuser__oxusername->value,
                ':oldUserId' => $this->oxuser__oxid->value,
            ]);
        }

        return $newUserId;
    }

    public function sendRegistrationEmail(bool $sendConfirmEmail = false): void
    {
        $email = oxNew(Email::class);
        if ($sendConfirmEmail) {
            $email->sendRegisterConfirmEmail($this);
        } else {
            $email->sendRegisterEmail($this);
        }
    }

    /**
     * Adds user to the group
     *
     * @param string $sGroupID group id
     *
     * @return bool
     */
    public function addToGroup($sGroupID)
    {
        if (!$this->inGroup($sGroupID)) {
            // create oxgroup object
            $oGroup = oxNew(\OxidEsales\Eshop\Application\Model\Groups::class);
            if ($oGroup->load($sGroupID)) {
                $oNewGroup = oxNew(\OxidEsales\Eshop\Application\Model\Object2Group::class);
                $oNewGroup->oxobject2group__oxobjectid = new \OxidEsales\Eshop\Core\Field($this->getId(), \OxidEsales\Eshop\Core\Field::T_RAW);
                $oNewGroup->oxobject2group__oxgroupsid = new \OxidEsales\Eshop\Core\Field($sGroupID, \OxidEsales\Eshop\Core\Field::T_RAW);
                if ($oNewGroup->save()) {
                    $this->_oGroups[$sGroupID] = $oGroup;

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Removes user from passed user group.
     *
     * @param string $sGroupID group id
     */
    public function removeFromGroup($sGroupID = null)
    {
        if ($sGroupID != null && $this->inGroup($sGroupID)) {
            $oGroups = oxNew(ListModel::class);
            $oGroups->init('oxobject2group');
            $sSelect = 'select * from oxobject2group
                where oxobject2group.oxobjectid = :oxobjectid
                and oxobject2group.oxgroupsid = :oxgroupsid ';
            $oGroups->selectString($sSelect, [
                ':oxobjectid' => $this->getId(),
                ':oxgroupsid' => $sGroupID
            ]);
            foreach ($oGroups as $oRemgroup) {
                if ($oRemgroup->delete()) {
                    unset($this->_oGroups[$oRemgroup->oxobject2group__oxgroupsid->value]);
                }
            }
        }
    }

    /**
     * Called after saving an order.
     *
     * @param object $oBasket  Shopping basket object
     * @param int    $iSuccess order success status
     */
    public function onOrderExecute($oBasket, $iSuccess)
    {
        if (is_numeric($iSuccess) && $iSuccess != 2 && $iSuccess <= 3) {
            //adding user to particular customer groups
            $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
            $dMidlleCustPrice = (float) $myConfig->getConfigParam('sMidlleCustPrice');
            $dLargeCustPrice = (float) $myConfig->getConfigParam('sLargeCustPrice');

            $this->addToGroup('oxidcustomer');
            $dBasketPrice = $oBasket->getPrice()->getBruttoPrice();
            if ($dBasketPrice < $dMidlleCustPrice) {
                $this->addToGroup('oxidsmallcust');
            }
            if ($dBasketPrice >= $dMidlleCustPrice && $dBasketPrice < $dLargeCustPrice) {
                $this->addToGroup('oxidmiddlecust');
            }
            if ($dBasketPrice >= $dLargeCustPrice) {
                $this->addToGroup('oxidgoodcust');
            }

            if ($this->inGroup('oxidnotyetordered')) {
                $this->removeFromGroup('oxidnotyetordered');
            }
        }
    }

    /**
     * Returns notice, wishlist or saved basket object
     *
     * @param string $sName name/type of basket
     *
     * @return \OxidEsales\Eshop\Application\Model\UserBasket
     */
    public function getBasket($sName)
    {
        if (!isset($this->_aBaskets[$sName])) {
            /** @var \OxidEsales\Eshop\Application\Model\UserBasket $oBasket */
            $oBasket = oxNew(\OxidEsales\Eshop\Application\Model\UserBasket::class);
            $aWhere = ['oxuserbaskets.oxuserid' => $this->getId(), 'oxuserbaskets.oxtitle' => $sName];

            $query = $oBasket->buildSelectString($aWhere);
            $record = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->select($query);
            if ($record && $record->count() > 0) {
                $oBasket->assign($record->fields);
            } else {
                //creating if it does not exist
                $oBasket->oxuserbaskets__oxtitle = new \OxidEsales\Eshop\Core\Field($sName);
                $oBasket->oxuserbaskets__oxuserid = new \OxidEsales\Eshop\Core\Field($this->getId());

                // marking basket as new (it will not be saved in DB yet)
                $oBasket->setIsNewBasket();
            }

            $this->_aBaskets[$sName] = $oBasket;
        }

        return $this->_aBaskets[$sName];
    }

    /**
     * User birthday converter. Usually this data comes in array form, so before
     * writing into DB it must be converted into string
     *
     * @param array $aData birthday data
     *
     * @return string
     */
    public function convertBirthday($aData)
    {

        // preparing data to process
        $iYear = isset($aData['year']) ? ((int) $aData['year']) : false;
        $iMonth = isset($aData['month']) ? ((int) $aData['month']) : false;
        $iDay = isset($aData['day']) ? ((int) $aData['day']) : false;

        // leaving empty if not set
        if (!$iYear && !$iMonth && !$iDay) {
            return "";
        }

        // year
        if (!$iYear || $iYear < 1000 || $iYear > 9999) {
            $iYear = date('Y');
        }

        // month
        if (!$iMonth || $iMonth < 1 || $iMonth > 12) {
            $iMonth = 1;
        }

        // maximum number of days in month
        $iMaxDays = 31;
        switch ($iMonth) {
            case 2:
                if ($iMaxDays > 28) {
                    $iMaxDays = ($iYear % 4 == 0 && ($iYear % 100 != 0 || $iYear % 400 == 0)) ? 29 : 28;
                }
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                $iMaxDays = min(30, $iMaxDays);
                break;
        }

        // day
        if (!$iDay || $iDay < 1 || $iDay > $iMaxDays) {
            $iDay = 1;
        }

        // whole date
        return sprintf("%04d-%02d-%02d", $iYear, $iMonth, $iDay);
    }

    /**
     * Return standard credit rating, can be set in config option iCreditRating;
     *
     * @return integer
     */
    public function getBoni()
    {
        if (!$iBoni = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('iCreditRating')) {
            $iBoni = 1000;
        }

        return $iBoni;
    }

    /**
     * Performs bunch of checks if user profile data is correct; on any
     * error exception is thrown
     *
     * @param string $sLogin      user login name
     * @param string $sPassword   user password
     * @param string $sPassword2  user password to compare
     * @param array  $aInvAddress array of user profile data
     * @param array  $aDelAddress array of user profile data
     *
     * @throws StandardException
     */
    public function checkValues($sLogin, $sPassword, $sPassword2, $aInvAddress, $aDelAddress)
    {
        /** @var \OxidEsales\Eshop\Core\InputValidator $oInputValidator */
        $oInputValidator = Registry::getInputValidator();

        // 1. checking user name
        $sLogin = $oInputValidator->checkLogin($this, $sLogin, $aInvAddress);

        // 2. checking email
        $oInputValidator->checkEmail($this, $sLogin);

        // 3. password
        $oInputValidator->checkPassword($this, $sPassword, $sPassword2, ((int) Registry::getRequest()->getRequestEscapedParameter('option') == 3));

        // 4. required fields
        $oInputValidator->checkRequiredFields($this, $aInvAddress, $aDelAddress);

        // 5. country check
        $oInputValidator->checkCountries($this, $aInvAddress, $aDelAddress);

        // 6. vat id check.
        try {
            $oInputValidator->checkVatId($this, $aInvAddress);
        } catch (\OxidEsales\Eshop\Core\Exception\ConnectionException $e) {
            // R080730 just oxInputException is passed here
            // if it oxConnectionException, it means it could not check vat id
            // and will set 'not checked' status to it later
        }

        // throwing first validation error
        if ($oError = Registry::getInputValidator()->getFirstValidationError()) {
            throw $oError;
        }
    }

    /**
     * Sets newsletter subscription status to user
     *
     * @param bool $blSubscribe       subscribes/unsubscribes user from newsletter
     * @param bool $blSendOptIn       if to send confirmation email
     * @param bool $blForceCheckOptIn forces to check subscription even when it is set to 1
     *
     * @return bool
     */
    public function setNewsSubscription($blSubscribe, $blSendOptIn, $blForceCheckOptIn = false)
    {
        // assigning to newsletter
        $blSuccess = false;

        // user wants to get newsletter messages or no ?
        $oNewsSubscription = $this->getNewsSubscription();
        if ($oNewsSubscription) {
            if ($blSubscribe && ($blForceCheckOptIn || ($iOptInStatus = $oNewsSubscription->getOptInStatus()) != 1)) {
                if (!$blSendOptIn) {
                    // double-opt-in check is disabled - assigning automatically
                    $this->addToGroup('oxidnewsletter');
                    // and setting subscribed status
                    $oNewsSubscription->setOptInStatus(1);
                    $blSuccess = true;
                } else {
                    // double-opt-in check enabled - sending confirmation email and setting waiting status
                    if ($iOptInStatus != 2) {
                        // sending double-opt-in mail
                        $oEmail = oxNew(Email::class);
                        $blSuccess = $oEmail->sendNewsletterDbOptInMail($this);
                    } else {
                        // mail already was sent, so just confirming that
                        $blSuccess = true;
                    }

                    $oNewsSubscription->setOptInStatus(2);
                }
            } elseif (!$blSubscribe) {
                // removing user from newsletter subscribers
                $this->removeFromGroup('oxidnewsletter');
                $oNewsSubscription->setOptInStatus(0);
                $blSuccess = true;
            }
        }

        return $blSuccess;
    }

    /**
     * When changing/updating user information in frontend this method validates user
     * input. If data is fine - automatically assigns this values. Additionally calls
     * methods (\OxidEsales\Eshop\Application\Model\User::_setAutoGroups, \OxidEsales\Eshop\Application\Model\User::setNewsSubscription) to perform automatic
     * groups assignment and returns newsletter subscription status. If some action
     * fails - exception is thrown.
     *
     * @param string $sUser       user login name
     * @param string $sPassword   user password
     * @param string $sPassword2  user confirmation password
     * @param array  $aInvAddress user billing address
     * @param array  $aDelAddress delivery address
     *
     * @throws StandardException
     */
    public function changeUserData($sUser, $sPassword, $sPassword2, $aInvAddress, $aDelAddress)
    {
        // validating values before saving. If validation fails - exception is thrown
        $this->checkValues($sUser, $sPassword, $sPassword2, $aInvAddress, $aDelAddress);
        // input data is fine - lets save updated user info

        $this->assign($aInvAddress);

        $this->onChangeUserData($aInvAddress);

        // update old or add new delivery address
        $this->assignAddress($aDelAddress);

        // saving new values
        if ($this->save()) {
            // assigning automatically to specific groups
            $sCountryId = isset($aInvAddress['oxuser__oxcountryid']) ? $aInvAddress['oxuser__oxcountryid'] : '';
            $this->setAutoGroups($sCountryId);
        }
    }

    /**
     * Returns merged delivery address fields.
     *
     * @return string
     */
    protected function getMergedAddressFields()
    {
        $sDelAddress = '';
        $sDelAddress .= $this->oxuser__oxcompany;
        $sDelAddress .= $this->oxuser__oxusername;
        $sDelAddress .= $this->oxuser__oxfname;
        $sDelAddress .= $this->oxuser__oxlname;
        $sDelAddress .= $this->oxuser__oxstreet;
        $sDelAddress .= $this->oxuser__oxstreetnr;
        $sDelAddress .= $this->oxuser__oxaddinfo;
        $sDelAddress .= $this->oxuser__oxustid;
        $sDelAddress .= $this->oxuser__oxcity;
        $sDelAddress .= $this->oxuser__oxcountryid;
        $sDelAddress .= $this->oxuser__oxstateid;
        $sDelAddress .= $this->oxuser__oxzip;
        $sDelAddress .= $this->oxuser__oxfon;
        $sDelAddress .= $this->oxuser__oxfax;
        $sDelAddress .= $this->oxuser__oxsal;

        return $sDelAddress;
    }

    /**
     * creates new address entry or updates existing
     *
     * @param array $aDelAddress address data array
     */
    protected function assignAddress($aDelAddress)
    {
        if (is_array($aDelAddress) && count($aDelAddress)) {
            $sAddressId = Registry::getRequest()->getRequestEscapedParameter('oxaddressid');
            $sAddressId = ($sAddressId === null || $sAddressId == -1 || $sAddressId == -2) ? null : $sAddressId;

            $oAddress = oxNew(\OxidEsales\Eshop\Application\Model\Address::class);
            $oAddress->setId($sAddressId);
            $oAddress->load($sAddressId);
            $oAddress->assign($aDelAddress);
            $oAddress->oxaddress__oxuserid = new \OxidEsales\Eshop\Core\Field($this->getId(), \OxidEsales\Eshop\Core\Field::T_RAW);
            $oAddress->oxaddress__oxcountry = $this->getUserCountry($oAddress->oxaddress__oxcountryid->value);
            $oAddress->save();

            // resetting addresses
            $this->_aAddresses = null;

            // saving delivery Address for later use
            Registry::getSession()->setVariable('deladrid', $oAddress->getId());
        } else {
            // resetting
            Registry::getSession()->setVariable('deladrid', null);
        }
    }

    /**
     * Builds and returns user login query.
     *
     * MD5 encoding is used in legacy eShop versions.
     * We still allow to perform the login for users registered in the previous eshop versions.
     *
     * @param string $userName login name
     * @param string $password login password
     * @param int    $shopId   shopid
     * @param bool   $isAdmin  admin/non admin mode
     *
     * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface`
     *                                        was added as the new default for hashing passwords. Hashing passwords with
     *                                        MD5 and SHA512 is still supported in order support login with older
     *                                        password hashes. Therefor this method might not be
     *                                        compatible with the current passhword hash any more.
     *
     * @return string
     */
    protected function _getLoginQueryHashedWithMD5($userName, $password, $shopId, $isAdmin) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $userNameCondition = $this->formQueryPartForUserName($userName, $database);
        $passwordCondition = $this->formQueryPartForMD5Password($password, $database);
        $shopOrRightsCondition = $this->formQueryPartForAdminView($shopId, $isAdmin);
        $userActiveCondition = $this->formQueryPartForActiveUser();

        $query = "SELECT `oxid`
                    FROM oxuser
                    WHERE 1
                    AND $userActiveCondition
                    AND $passwordCondition
                    AND $userNameCondition
                    $shopOrRightsCondition
                    ";

        return $query;
    }

    /**
     * Builds and returns user login query
     *
     * @param string $userName
     * @param string $password
     * @param int    $shopId
     * @param bool   $isAdmin
     *
     * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface`
     *                                        was added as the new default for hashing passwords. Hashing passwords with
     *                                        MD5 and SHA512 is still supported in order support login with older
     *                                        password hashes. Therefor this method might not be
     *                                        compatible with the current passhword hash any more.
     *
     * @return string
     */
    protected function _getLoginQuery($userName, $password, $shopId, $isAdmin) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $database = DatabaseProvider::getDb();
        $userNameCondition = $this->formQueryPartForUserName($userName, $database);
        $shopOrRightsCondition = $this->formQueryPartForAdminView($shopId, $isAdmin);
        $passwordCondition = $this->formQueryPartForSha512Password($password, $database, $userNameCondition, $shopOrRightsCondition);
        $userActiveCondition = $this->formQueryPartForActiveUser();


        $query = "SELECT `oxid`
                    FROM oxuser
                    WHERE 1
                    AND $userActiveCondition
                    AND $passwordCondition
                    AND $userNameCondition
                    $shopOrRightsCondition
                    ";

        return $query;
    }

    /**
     * Performs user login by username and password. Fetches user data from DB.
     * Registers in session. Returns true on success, FALSE otherwise.
     *
     * NOTE: It the user has already been loaded prior calling \OxidEsales\Eshop\Application\Model\User::login,
     * NO valid password is necessary for login.
     *
     * @param string $userName         User username
     * @param string $password         User password
     * @param bool   $setSessionCookie (default false)
     *
     * @throws CookieException
     * @throws UserException
     *
     * @return bool
     */
    public function login($userName, $password, $setSessionCookie = false)
    {
        $isLoginAttemptToAdminBackend = $this->isAdmin();

        $cookie = Registry::getUtilsServer()->getOxCookie();
        if ($cookie === null && $isLoginAttemptToAdminBackend) {
            throw oxNew(CookieException::class, 'ERROR_MESSAGE_COOKIE_NOCOOKIE');
        }

        $config = Registry::getConfig();
        $shopId = $config->getShopId();

        /** New authentication mechanism */
        $passwordHashFromDatabase = $this->getPasswordHashFromDatabase($userName, $shopId, $isLoginAttemptToAdminBackend);
        $passwordServiceBridge = $this->getContainer()->get(PasswordServiceBridgeInterface::class);
        if ($password && !$this->isLoaded()) {
            $userIsAuthenticated = $passwordServiceBridge->verifyPassword($password, $passwordHashFromDatabase);
            if ($userIsAuthenticated) {
                $this->loadAuthenticatedUser($userName, $shopId);
            }
        }

        /** Old authentication + authorization */
        if ($password && !$this->isLoaded()) {
            $this->_dbLogin($userName, $password, $shopId);
        }

        /** If needed, store a rehashed password with the authenticated user */
        if ($password && $this->isLoaded()) {
            $passwordNeedsRehash = $this->isOutdatedPasswordHashAlgorithmUsed ||
                                   $passwordServiceBridge->passwordNeedsRehash($passwordHashFromDatabase);
            if ($passwordNeedsRehash) {
                $generatedPasswordHash = $this->hashPassword($password);
                $this->oxuser__oxpassword = new Field($generatedPasswordHash, Field::T_RAW);
                /** The use of a salt is deprecated and an empty salt will be stored */
                $this->oxuser__oxpasssalt = new Field('');
                $this->save();
            }
        }

        /** Event for alternative authentication and authorization mechanisms, or whatsoever */
        $this->onLogin($userName, $password);

        /**
         * If the user has not been loaded until this point, authentication & authorization is considered as failed.
         */
        if (!$this->isLoaded()) {
            throw oxNew(UserException::class, 'ERROR_MESSAGE_USER_NOVALIDLOGIN');
        }

        //resetting active user
        $this->setUser(null);

        if ($isLoginAttemptToAdminBackend) {
            Registry::getSession()->setVariable('auth', $this->oxuser__oxid->value);
        } else {
            Registry::getSession()->setVariable('usr', $this->oxuser__oxid->value);
        }

        // cookie must be set ?
        if ($setSessionCookie && $config->getConfigParam('blShowRememberMe')) {
            Registry::getUtilsServer()->setUserCookie(
                $this->oxuser__oxusername->value,
                $this->oxuser__oxpassword->value,
                $config->getShopId(),
                31536000,
                static::USER_COOKIE_SALT
            );
        }

        return true;
    }

    /**
     * @param string $userName
     * @param int    $shopId
     *
     * @throws UserException
     */
    private function loadAuthenticatedUser(string $userName, int $shopId)
    {
        $isLoginToAdminBackend = $this->isAdmin();
        $userId = $this->getAuthenticatedUserId($userName, $shopId, $isLoginToAdminBackend);
        if (!$this->load($userId)) {
            throw oxNew(UserException::class, 'ERROR_MESSAGE_USER_NOVALIDLOGIN');
        }
    }

    /**
     * @param string $userName
     * @param int    $shopId
     * @param bool   $isLoginToAdminBackend
     *
     * @return false|string
     */
    private function getAuthenticatedUserId(string $userName, int $shopId, bool $isLoginToAdminBackend)
    {
        $database = DatabaseProvider::getDb();
        $userNameCondition = $this->formQueryPartForUserName($userName, $database);
        $shopOrRightsCondition = $this->formQueryPartForAdminView($shopId, $isLoginToAdminBackend);
        $userActiveCondition = $this->formQueryPartForActiveUser();

        $query = "SELECT `OXID`
                    FROM oxuser
                    WHERE 1
                    AND $userActiveCondition
                    AND $userNameCondition
                    $shopOrRightsCondition
                    ";

        return $database->getOne($query);
    }

    /**
     * Logs out session user. Returns true on success
     *
     * @return bool
     */
    public function logout()
    {
        // deleting session info
        Registry::getSession()->deleteVariable('usr'); // for front end
        Registry::getSession()->deleteVariable('auth'); // for back end
        Registry::getSession()->deleteVariable('dynvalue');
        Registry::getSession()->deleteVariable('paymentid');
        // Registry::getSession()->deleteVariable( 'deladrid' );

        // delete cookie
        Registry::getUtilsServer()->deleteUserCookie(\OxidEsales\Eshop\Core\Registry::getConfig()->getShopID());

        // unsetting global user
        $this->setUser(null);

        return true;
    }

    /**
     * Loads active admin user object (if possible). If
     * user is not available - returns false.
     *
     * @return bool
     */
    public function loadAdminUser()
    {
        return $this->loadActiveUser(true);
    }

    /**
     * Loads active user object. If
     * user is not available - returns false.
     *
     * @param bool $blForceAdmin (default false)
     *
     * @return bool
     */
    public function loadActiveUser($blForceAdmin = false)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $blAdmin = $this->isAdmin() || $blForceAdmin;

        // first - checking session info
        $sUserID = $blAdmin ? Registry::getSession()->getVariable('auth') : Registry::getSession()->getVariable('usr');

        // trying automatic login (by 'remember me' cookie)
        $blFoundInCookie = false;
        if (!$sUserID && !$blAdmin && $oConfig->getConfigParam('blShowRememberMe')) {
            $sUserID = $this->getCookieUserId();
            $blFoundInCookie = $sUserID ? true : false;
        }

        // checking user results
        if ($sUserID) {
            if ($this->load($sUserID)) {
                // storing into session
                if ($blAdmin) {
                    Registry::getSession()->setVariable('auth', $sUserID);
                } else {
                    Registry::getSession()->setVariable('usr', $sUserID);
                }

                // marking the way user was loaded
                $this->_blLoadedFromCookie = $blFoundInCookie;

                return true;
            }
        } else {
            // no user
            if ($blAdmin) {
                Registry::getSession()->deleteVariable('auth');
            } else {
                Registry::getSession()->deleteVariable('usr');
            }

            return false;
        }
    }

    /**
     * Checks if user is connected via cookies and if so, returns user id.
     *
     * @return string
     */
    protected function getCookieUserId()
    {
        $sUserID = null;
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sShopID = $oConfig->getShopId();
        if (($sSet = Registry::getUtilsServer()->getUserCookie($sShopID))) {
            $passwordServiceBridge = $this->getContainer()->get(PasswordServiceBridgeInterface::class);
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $aData = explode('@@@', $sSet);
            $sUser = $aData[0];
            $sPWD = @$aData[1];

            $sSelect = $this->formUserCookieQuery($sUser, $sShopID);
            $rs = $oDb->select($sSelect);
            if ($rs != false && $rs->count() > 0) {
                while (!$rs->EOF) {
                    if ($passwordServiceBridge->verifyPassword($rs->fields[1] . static::USER_COOKIE_SALT, $sPWD)) {
                        // found
                        $sUserID = $rs->fields[0];
                        break;
                    }
                    $rs->fetchRow();
                }
            }
            // if cookie info is not valid, remove it.
            if (!$sUserID) {
                Registry::getUtilsServer()->deleteUserCookie($sShopID);
            }
        }

        return $sUserID;
    }

    /**
     * Returns user rights index. Index cannot be higher than current session
     * user rights index.
     *
     * @return string
     */
    protected function getUserRights()
    {
        // previously user had no rights defined
        if (!$this->oxuser__oxrights instanceof \OxidEsales\Eshop\Core\Field || !$this->oxuser__oxrights->value) {
            return 'user';
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sAuthRights = null;

        // choosing possible user rights index
        $sAuthUserID = $this->isAdmin() ? Registry::getSession()->getVariable('auth') : null;
        $sAuthUserID = $sAuthUserID ? $sAuthUserID : Registry::getSession()->getVariable('usr');
        if ($sAuthUserID) {
            $authRightsSql = 'select oxrights from ' . $this->getViewName() . ' where oxid = :oxid';
            $sAuthRights = $oDb->getOne($authRightsSql, [
                ':oxid' => $sAuthUserID
            ]);
        }

        //preventing user rights edit for non admin
        $aRights = [];

        // selecting current users rights ...
        $currentRightsSql = 'select oxrights from ' . $this->getViewName() . ' where oxid = :oxid';
        $params = [
            ':oxid' => $this->getId()
        ];
        if ($sCurrRights = $oDb->getOne($currentRightsSql, $params)) {
            $aRights[] = $sCurrRights;
        }
        $aRights[] = 'user';

        if (!$sAuthRights || !($sAuthRights == 'malladmin' || $sAuthRights == $myConfig->getShopId())) {
            return current($aRights);
        } elseif ($sAuthRights == $myConfig->getShopId()) {
            $aRights[] = $sAuthRights;
            if (!in_array($this->oxuser__oxrights->value, $aRights)) {
                return current($aRights);
            }
        }

        // leaving as it was set ...
        return $this->oxuser__oxrights->value;
    }

    /**
     * Inserts user object data to DB. Returns true on success.
     *
     * @return bool
     */
    protected function insert()
    {

        // set oxcreate date
        $this->oxuser__oxcreate = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s'), \OxidEsales\Eshop\Core\Field::T_RAW);

        if (!isset($this->oxuser__oxboni->value)) {
            $this->oxuser__oxboni = new \OxidEsales\Eshop\Core\Field($this->getBoni(), \OxidEsales\Eshop\Core\Field::T_RAW);
        }

        return parent::insert();
    }

    /**
     * Updates changed user object data to DB. Returns true on success.
     *
     * @return bool
     */
    protected function update()
    {
        //V #M418: for not registered users, don't change boni during update
        if (!$this->getFieldData('oxpassword') && $this->getFieldData('oxregister') < 1) {
            $this->_aSkipSaveFields[] = 'oxboni';
        }

        // don't change this field
        $this->_aSkipSaveFields[] = 'oxcreate';
        if (!$this->isAdmin()) {
            $this->_aSkipSaveFields[] = 'oxcustnr';
            $this->_aSkipSaveFields[] = 'oxrights';
        }

        // updating subscription information
        if (($blUpdate = parent::update())) {
            $this->getNewsSubscription()->updateSubscription($this);
        }

        return $blUpdate;
    }

    /**
     * Checks for already used email
     *
     * @param string $sEmail user email/login
     *
     * @return bool
     */
    public function checkIfEmailExists($sEmail)
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $iShopId = $myConfig->getShopId();
        $blExists = false;

        $sQ = 'select oxshopid, oxrights, oxpassword from oxuser where oxusername = :oxusername';
        $params = [
            ':oxusername' => (string) $sEmail
        ];
        if (($sOxid = $this->getId())) {
            $sQ .= " and oxid <> :notoxid";
            $params[':notoxid'] = $sOxid;
        }
        $oRs = $masterDb->select($sQ, $params);
        if ($oRs != false && $oRs->count() > 0) {
            if ($this->_blMallUsers) {
                $blExists = true;
                if ($oRs->fields[1] == 'user' && !$oRs->fields[2]) {
                    // password is not set - allow to override
                    $blExists = false;
                }
            } else {
                $blExists = false;
                while (!$oRs->EOF) {
                    if ($oRs->fields[1] != 'user') {
                        // exists admin with same login - must not allow
                        $blExists = true;
                        break;
                    } elseif ($oRs->fields[0] == $iShopId && $oRs->fields[2]) {
                        // exists same login (with password) in same shop
                        $blExists = true;
                        break;
                    }

                    $oRs->fetchRow();
                }
            }
        }

        return $blExists;
    }

    /**
     * Returns user recommendation list object
     *
     * @param string $sOXID object ID (default is null)
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return ListModel with oxrecommlist objects
     */
    public function getUserRecommLists($sOXID = null)
    {
        if (!$sOXID) {
            $sOXID = $this->getId();
        }

        // sets active page
        $iActPage = (int) Registry::getRequest()->getRequestEscapedParameter('pgNr');
        $iActPage = ($iActPage < 0) ? 0 : $iActPage;

        // load only lists which we show on screen
        $iNrofCatArticles = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('iNrofCatArticles');
        $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;


        $oRecommList = oxNew(ListModel::class);
        $oRecommList->init('oxrecommlist');
        $oRecommList->setSqlLimit($iNrofCatArticles * $iActPage, $iNrofCatArticles);
        $iShopId = Registry::getConfig()->getShopId();
        $sSelect = 'select * from oxrecommlists
            where oxuserid = :oxuserid
                and oxshopid = :oxshopid';
        $oRecommList->selectString($sSelect, [
            ':oxuserid' => $sOXID,
            ':oxshopid' => $iShopId
        ]);

        return $oRecommList;
    }

    /**
     * Returns recommlist count
     *
     * @param string $sOx object ID (default is null)
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return int
     */
    public function getRecommListsCount($sOx = null)
    {
        if (!$sOx) {
            $sOx = $this->getId();
        }

        if ($this->_iCntRecommLists === null || $sOx) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $this->_iCntRecommLists = 0;
            $iShopId = Registry::getConfig()->getShopId();
            $sSelect = 'select count(oxid) from oxrecommlists
                where oxuserid = :oxuserid and oxshopid = :oxshopid';
            $this->_iCntRecommLists = $oDb->getOne($sSelect, [
                ':oxuserid' => $sOx,
                ':oxshopid' => $iShopId
            ]);
        }

        return $this->_iCntRecommLists;
    }

    /**
     * Automatically assigns user to specific groups
     * according to users country information
     *
     * @param string $sCountryId users country id
     */
    protected function setAutoGroups($sCountryId)
    {
        // assigning automatically to specific groups
        $blForeigner = true;
        $blForeignGroupExists = false;
        $blInlandGroupExists = false;

        $aHomeCountry = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aHomeCountry');
        // foreigner ?
        if (is_array($aHomeCountry)) {
            if (in_array($sCountryId, $aHomeCountry)) {
                $blForeigner = false;
            }
        } elseif ($sCountryId == $aHomeCountry) {
            $blForeigner = false;
        }

        if ($this->inGroup('oxidforeigncustomer')) {
            $blForeignGroupExists = true;
            if (!$blForeigner) {
                $this->removeFromGroup('oxidforeigncustomer');
            }
        }

        if ($this->inGroup('oxidnewcustomer')) {
            $blInlandGroupExists = true;
            if ($blForeigner) {
                $this->removeFromGroup('oxidnewcustomer');
            }
        }

        if (!$blForeignGroupExists && $blForeigner) {
            $this->addToGroup('oxidforeigncustomer');
        }
        if (!$blInlandGroupExists && !$blForeigner) {
            $this->addToGroup('oxidnewcustomer');
        }
    }

    /**
     * Tries to load user object by passed update id. Update id is
     * generated when user forgot passwords and wants to update it
     *
     * @param string $sUid update id
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    public function loadUserByUpdateId($sUid)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select oxid from " . $this->getViewName() . "
            where oxupdateexp >= :time
                and MD5( CONCAT( oxid, oxshopid, oxupdatekey ) ) = :hash";
        if ($sUserId = $oDb->getOne($sQ, [':time' => time(), ':hash' => $sUid])) {
            return $this->load($sUserId);
        }
    }

    /**
     * Generates or resets and saves users update key
     *
     * @param bool $reset marker to reset update info
     */
    public function setUpdateKey($reset = false)
    {
        $token = $reset ? '' : $this->getRandomToken();
        $tokenExpirationTime = $reset ? 0 : Registry::getUtilsDate()->getTime() + $this->getUpdateLinkTerm();

        $this->oxuser__oxupdatekey = new Field($token, Field::T_RAW);
        $this->oxuser__oxupdateexp = new Field($tokenExpirationTime, Field::T_RAW);
        $this->save();
    }

    /**
     * Return password update link validity term (seconds). Default 3600 * 6
     *
     * @return int
     */
    public function getUpdateLinkTerm()
    {
        return 3600 * 6;
    }

    /**
     * Checks if password update key is not expired yet
     *
     * @param string $sKey key
     *
     * @return bool
     */
    public function isExpiredUpdateId($sKey)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select 1 from " . $this->getViewName() . "
            where oxupdateexp >= :time
            and MD5( CONCAT( oxid, oxshopid, oxupdatekey ) ) = :hash";

        return !((bool) $oDb->getOne($sQ, [':time' => time(), ':hash' => $sKey]));
    }

    /**
     * Returns user passwords update id
     *
     * @return string
     */
    public function getUpdateId()
    {
        if ($this->_sUpdateKey === null) {
            $this->setUpdateKey();
            $this->_sUpdateKey = md5($this->getId() . $this->oxuser__oxshopid->value . $this->oxuser__oxupdatekey->value);
        }

        return $this->_sUpdateKey;
    }

    /**
     * Encodes and returns given password
     *
     * @param string $sPassword password to encode
     * @param string $sSalt     any unique string value
     * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface`
     *                                        was added as the new default for hashing passwords. Hashing passwords with
     *                                        MD5 and SHA512 is still supported in order support login with older
     *                                        password hashes. Therefor this method might not be
     *                                        compatible with the current passhword hash any more.
     *
     * @return string
     */
    public function encodePassword($sPassword, $sSalt)
    {
        $oSha512Hasher = oxNew(\OxidEsales\Eshop\Core\Sha512Hasher::class);
        $oHasher = oxNew(\OxidEsales\Eshop\Core\PasswordHasher::class, $oSha512Hasher);

        return $oHasher->hash($sPassword, $sSalt);
    }
    /**
     * Sets new password for user ( save is not called)
     *
     * @param string $password password
     */
    public function setPassword($password = null)
    {
        if (empty($password)) {
            $passwordHash = '';
        } else {
            $passwordHash = $this->hashPassword($password);
        }

        $this->oxuser__oxpassword = new \OxidEsales\Eshop\Core\Field($passwordHash, \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->oxuser__oxpasssalt = new \OxidEsales\Eshop\Core\Field('');
    }

    /**
     * @param string $password
     *
     * @return string
     */
    private function hashPassword(string $password): string
    {
        $passwordServiceBridge = $this->getContainer()->get(PasswordServiceBridgeInterface::class);

        return $passwordServiceBridge->hash($password);
    }

    /**
     * Checks if user entered password is the same as old
     *
     * @param string $password new password
     *
     * @return bool
     */
    public function isSamePassword($password)
    {
        return  password_verify($password, $this->oxuser__oxpassword->value);
    }

    /**
     * Returns if user was loaded from cookie
     *
     * @return bool
     */
    public function isLoadedFromCookie()
    {
        return $this->_blLoadedFromCookie;
    }

    /**
     * Generates user password and username hash for review
     *
     * @param string $sUserId userid
     *
     * @return string
     */
    public function getReviewUserHash($sUserId)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $hashSql = 'select md5(concat("oxid", oxpassword, oxusername )) from oxuser
            where oxid = :oxid';
        $sReviewUserHash = $oDb->getOne($hashSql, [
            ':oxid' => $sUserId
        ]);

        return $sReviewUserHash;
    }

    /**
     * Gets from review user hash user id
     *
     * @param string $sReviewUserHash review user hash
     *
     * @return string
     */
    public function getReviewUserId($sReviewUserHash)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $userIdSql = 'select oxid from oxuser where md5(concat("oxid", oxpassword, oxusername )) = :hash';
        $sUserId = $oDb->getOne($userIdSql, [
            ':hash' => $sReviewUserHash
        ]);

        return $sUserId;
    }

    /**
     * Get state id for current user
     *
     * @return mixed
     */
    public function getStateId()
    {
        return $this->oxuser__oxstateid->value;
    }

    /**
     * Get state title by id
     *
     * @param string $sId state ID
     *
     * @return string
     */
    public function getStateTitle($sId = null)
    {
        $oState = $this->getStateObject();

        if (is_null($sId)) {
            $sId = $this->getStateId();
        }

        return $oState->getTitleById($sId);
    }

    /**
     * Checks if user accepted latest shopping terms and conditions version
     *
     * @return bool
     */
    public function isTermsAccepted()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $termsSql = "select 1 from oxacceptedterms where oxuserid = :oxuserid and oxshopid = :oxshopid";
        return (bool) $oDb->getOne($termsSql, [
            ':oxuserid' => $this->getId(),
            ':oxshopid' => Registry::getConfig()->getShopId()
        ]);
    }

    /**
     * Writes terms acceptance info to db
     */
    public function acceptTerms()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sUserId = $oDb->quote($this->getId());
        $sShopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId();
        $sVersion = oxNew(\OxidEsales\Eshop\Application\Model\Content::class)->getTermsVersion();

        $oDb->execute("replace oxacceptedterms set oxuserid={$sUserId}, oxshopid='{$sShopId}', oxtermversion='{$sVersion}'");
    }

    /**
     * Assigns registration points for invited user and
     * its inviter (calls \OxidEsales\Eshop\Application\Model\User::setInvitationCreditPoints())
     *
     * @param string $sUserId   inviter user id
     * @param string $sRecEmail recipient (registrant) email
     *
     * @return bool
     */
    public function setCreditPointsForRegistrant($sUserId, $sRecEmail)
    {
        $blSet = false;
        $iPoints = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('dPointsForRegistration');
        // check if this invitation is still not accepted
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $pendingSql = "select count(oxuserid) from oxinvitations
            where oxuserid = :oxuserid
                and md5(oxemail) = :oxemailhash
                and oxpending = :oxpending
                and oxaccepted = :oxaccepted";
        $iPending = $masterDb->getOne($pendingSql, [
            ':oxuserid' => $sUserId,
            ':oxemailhash' => $sRecEmail,
            ':oxpending' => 1,
            ':oxaccepted' => 0
        ]);
        if ($iPoints && $iPending) {
            $this->oxuser__oxpoints = new Field($iPoints, Field::T_RAW);
            if ($blSet = $this->save()) {
                // updating users statistics
                $query = "UPDATE oxinvitations
                          SET oxpending = '0',
                              oxaccepted = '1'
                          WHERE oxuserid = :oxuserid AND
                                md5(oxemail) = :oxemail";
                $masterDb->execute($query, [
                    ':oxuserid' => $sUserId,
                    ':oxemail' => $sRecEmail
                ]);
                $oInvUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
                if ($oInvUser->load($sUserId)) {
                    $blSet = $oInvUser->setCreditPointsForInviter();
                }
            }
        }
        Registry::getSession()->deleteVariable('su');
        Registry::getSession()->deleteVariable('re');

        return $blSet;
    }

    /**
     * Assigns credit points to inviter
     *
     * @return bool
     */
    public function setCreditPointsForInviter()
    {
        $blSet = false;
        $iPoints = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('dPointsForInvitation');
        if ($iPoints) {
            $iNewPoints = $this->oxuser__oxpoints->value + $iPoints;
            $this->oxuser__oxpoints = new Field($iNewPoints, Field::T_RAW);
            $blSet = $this->save();
        }

        return $blSet;
    }

    /**
     * Updating invitations statistics
     *
     * @param array $aRecEmail array of recipients emails
     */
    public function updateInvitationStatistics($aRecEmail)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sUserId = $this->getId();

        if ($sUserId && is_array($aRecEmail) && count($aRecEmail) > 0) {
            //iserting statistics about invitation
            $sDate = Registry::getUtilsDate()->formatDBDate(date("Y-m-d"), true);
            foreach ($aRecEmail as $sRecEmail) {
                $sSql = "INSERT INTO oxinvitations SET oxuserid = :oxuserid, oxemail = :oxemail, oxdate = :oxdate, oxpending = '1', oxaccepted = '0', oxtype = '1'";
                $oDb->execute($sSql, [
                    ':oxuserid' => $sUserId,
                    ':oxemail' => $sRecEmail,
                    ':oxdate' => $sDate
                ]);
            }
        }
    }

    /**
     * return user id by user name
     *
     * @param string $userName
     *
     * @return false|string
     */
    public function getIdByUserName($userName)
    {
        $userId = DatabaseProvider::getDb()
            ->getOne(
                'SELECT `OXID` FROM `oxuser` WHERE `OXUSERNAME` = :oxusername AND `OXSHOPID` = :oxshopid',
                [
                    ':oxusername' => (string) $userName,
                    ':oxshopid' => Registry::getConfig()->getShopId()
                ]
            );

        return $userId;
    }

    /**
     * returns true if user registered and have account
     *
     * @return bool
     */
    public function hasAccount()
    {
        return (bool) $this->oxuser__oxpassword->value;
    }

    /**
     * Return user price view mode, true - if netto mode
     *
     * @return bool
     */
    public function isPriceViewModeNetto()
    {
        return (bool) \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blShowNetPrice');
    }

    /**
     * Returns true if User is mall admin.
     *
     * @return bool
     */
    public function isMallAdmin()
    {
        return 'malladmin' === $this->oxuser__oxrights->value;
    }

    /**
     * Initiates user login against data in DB.
     *
     * @param string $userName User
     * @param string $password Password
     * @param int    $shopId   Shop id
     *
     * @throws UserException
     *
     * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface`
     *                                        was added as the new default for hashing passwords. Hashing passwords with
     *                                        MD5 and SHA512 is still supported in order support login with older
     *                                        password hashes. Therefor this method might not be
     *                                        compatible with the current passhword hash any more.
     *
     * @return void
     */
    protected function _dbLogin(string $userName, $password, $shopId) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $userId = $database->getOne($this->_getLoginQuery($userName, $password, $shopId, $this->isAdmin()));
        if (!$userId) {
            $userId = $database->getOne($this->_getLoginQueryHashedWithMD5($userName, $password, $shopId, $this->isAdmin()));
        }

        /** Return here to give other log-in mechanisms the possibility to be triggered */
        if (!$userId) {
            return;
        }

        $this->loadAuthenticatedUser($userName, $shopId);
        $this->isOutdatedPasswordHashAlgorithmUsed = true;
    }

    /**
     * @param string $userName
     * @param int    $shopId
     * @param bool   $isLoginToAdminBackend
     *
     * @return false|string
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    protected function getPasswordHashFromDatabase(string $userName, int $shopId, bool $isLoginToAdminBackend)
    {
        $database = DatabaseProvider::getDb();
        $userNameCondition = $this->formQueryPartForUserName($userName, $database);
        $shopOrRightsCondition = $this->formQueryPartForAdminView($shopId, $isLoginToAdminBackend);
        $userActiveCondition = $this->formQueryPartForActiveUser();

        $query = "SELECT `oxpassword`
                    FROM oxuser
                    WHERE 1
                    AND $userActiveCondition
                    AND $userNameCondition
                    $shopOrRightsCondition
                    ";

        return $database->getOne($query);
    }

    /**
     * Return true - if shop is in demo mode
     *
     * @return bool
     */
    protected function isDemoShop()
    {
        $blDemoMode = false;

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->isDemoShop()) {
            $blDemoMode = true;
        }

        return $blDemoMode;
    }

    /**
     * Return sql to get id of mall admin in demo shop
     *
     * @param string $sUser     User name
     * @param string $sPassword User password
     *
     * @throws object $oEx
     *
     * @return string
     */
    protected function getDemoShopLoginQuery($sUser, $sPassword)
    {
        if ($sPassword == "admin" && $sUser == "admin") {
            $sSelect = "SELECT `oxid` FROM `oxuser` WHERE `oxrights` = 'malladmin' ";
        } else {
            /** @var UserException $oEx */
            $oEx = oxNew(UserException::class);
            $oEx->setMessage('ERROR_MESSAGE_USER_NOVALIDLOGIN');
            throw $oEx;
        }

        return $sSelect;
    }

    /**
     * Method used for override.
     *
     * @param array $aInvAddress
     */
    protected function onChangeUserData($aInvAddress)
    {
    }

    /**
     * Method is used to make additional delete actions.
     *
     * @param string $sOXIDQuoted
     */
    protected function deleteAdditionally($sOXIDQuoted)
    {
    }

    /**
     * Updates query for selecting orders.
     *
     * @param string $query
     *
     * @return string
     */
    protected function updateGetOrdersQuery($query)
    {
        return $query;
    }

    /**
     * Method is used for overriding and add additional actions when logging in.
     *
     * @param string $userName
     * @param string $password
     */
    protected function onLogin($userName, $password)
    {
        /** Demo shop log in */
        if (!$this->isLoaded() && $this->isDemoShop() && $this->isAdmin()) {
            $database = DatabaseProvider::getDb();
            $userId = $database->getOne($this->getDemoShopLoginQuery($userName, $password));
            if ($userId) {
                $this->load($userId);
            }
        }
    }

    /**
     * Deletes User from groups.
     *
     * @param DatabaseInterface $database
     */
    private function deleteUserFromGroups(DatabaseInterface $database)
    {
        $database->execute('delete from oxobject2group where oxobject2group.oxobjectid = :oxobjectid', [
            ':oxobjectid' => $this->getId()
        ]);
    }

    /**
     * Deletes deliveries.
     *
     * @param DatabaseInterface $database
     */
    private function deleteDeliveries(DatabaseInterface $database)
    {
        $database->execute('delete from oxobject2delivery where oxobjectid = :oxobjectid', [
            ':oxobjectid' => $this->getId()
        ]);
    }

    /**
     * Deletes discounts.
     *
     * @param DatabaseInterface $database
     */
    private function deleteDiscounts(DatabaseInterface $database)
    {
        $database->execute('delete from oxobject2discount where oxobjectid = :oxobjectid', [
            ':oxobjectid' => $this->getId()
        ]);
    }

    /**
     * Deletes user accepted terms.
     *
     * @param DatabaseInterface $database
     */
    private function deleteAcceptedTerms(DatabaseInterface $database)
    {
        $database->execute('delete from oxacceptedterms where oxuserid = :oxuserid', [
            ':oxuserid' => $this->getId()
        ]);
    }

    /**
     * Deletes User addresses.
     *
     * @param DatabaseInterface $database
     */
    private function deleteAddresses(DatabaseInterface $database)
    {
        $ids = $database->getCol('SELECT oxid FROM oxaddress WHERE oxuserid = :oxuserid', [
            ':oxuserid' => $this->getId()
        ]);
        array_walk($ids, [$this, 'deleteItemById'], \OxidEsales\Eshop\Application\Model\Address::class);
    }

    /**
     * Deletes noticelists, wishlists or saved baskets
     *
     * @param DatabaseInterface $database
     */
    private function deleteBaskets(DatabaseInterface $database)
    {
        $ids = $database->getCol('SELECT oxid FROM oxuserbaskets WHERE oxuserid = :oxuserid', [
            ':oxuserid' => $this->getId()
        ]);
        array_walk($ids, [$this, 'deleteItemById'], \OxidEsales\Eshop\Application\Model\UserBasket::class);
    }

    /**
     * Deletes not Order related remarks.
     *
     * @param DatabaseInterface $database
     */
    private function deleteNotOrderRelatedRemarks(DatabaseInterface $database)
    {
        $sql = 'SELECT oxid FROM oxremark WHERE oxparentid = :oxparentid and oxtype != :notoxtype';
        $ids = $database->getCol($sql, [
            ':oxparentid' => $this->getId(),
            ':notoxtype' => 'o'
        ]);
        array_walk($ids, [$this, 'deleteItemById'], \OxidEsales\Eshop\Application\Model\Remark::class);
    }

    /**
     * Deletes recommendation lists.
     *
     * @param DatabaseInterface $database
     */
    private function deleteRecommendationLists(DatabaseInterface $database)
    {
        $ids = $database->getCol('SELECT oxid FROM oxrecommlists WHERE oxuserid = :oxuserid ', [
            ':oxuserid' => $this->getId()
        ]);
        array_walk($ids, [$this, 'deleteItemById'], \OxidEsales\Eshop\Application\Model\RecommendationList::class);
    }

    /**
     * Deletes newsletter subscriptions.
     *
     * @param DatabaseInterface $database
     */
    private function deleteNewsletterSubscriptions(DatabaseInterface $database)
    {
        $ids = $database->getCol('SELECT oxid FROM oxnewssubscribed WHERE oxuserid = :oxuserid ', [
            ':oxuserid' => $this->getId()
        ]);
        array_walk($ids, [$this, 'deleteItemById'], \OxidEsales\Eshop\Application\Model\NewsSubscribed::class);
    }


    /**
     * Deletes User reviews.
     *
     * @param DatabaseInterface $database
     */
    private function deleteReviews(DatabaseInterface $database)
    {
        $ids = $database->getCol('select oxid from oxreviews where oxuserid = :oxuserid', [
            ':oxuserid' => $this->getId()
        ]);
        array_walk($ids, [$this, 'deleteItemById'], \OxidEsales\Eshop\Application\Model\Review::class);
    }

    /**
     * Deletes User ratings.
     *
     * @param DatabaseInterface $database
     */
    private function deleteRatings(DatabaseInterface $database)
    {
        $ids = $database->getCol('SELECT oxid FROM oxratings WHERE oxuserid = :oxuserid', [
            ':oxuserid' => $this->getId()
        ]);
        array_walk($ids, [$this, 'deleteItemById'], \OxidEsales\Eshop\Application\Model\Rating::class);
    }

    /**
     * Deletes price alarms.
     *
     * @param DatabaseInterface $database
     */
    private function deletePriceAlarms(DatabaseInterface $database)
    {
        $ids = $database->getCol('SELECT oxid FROM oxpricealarm WHERE oxuserid = :oxuserid', [
            ':oxuserid' => $this->getId()
        ]);
        array_walk($ids, [$this, 'deleteItemById'], \OxidEsales\Eshop\Application\Model\PriceAlarm::class);
    }

    /**
     * Callback function for array_walk to delete items using the delete method of the given model class
     *
     * @param string  $id        Id of the item to be deleted
     * @param integer $key       Key of the array
     * @param string  $className Model class to be used
     */
    private function deleteItemById($id, $key, $className)
    {
        /** @var \OxidEsales\Eshop\Core\Model\BaseModel $modelObject */
        $modelObject = oxNew($className);

        if ($modelObject->load($id)) {
            if ($this->_blMallUsers) {
                $modelObject->setIsDerived(false);
            }
            $modelObject->delete();
        }
    }

    /**
     * @param string            $password
     * @param DatabaseInterface $database
     * @param string            $userCondition
     * @param string            $shopCondition
     *
     * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface`
     *                                        was added as the new default for hashing passwords. Hashing passwords with
     *                                        MD5 and SHA512 is still supported in order support login with older
     *                                        password hashes. Therefor this method might not be
     *                                        compatible with the current passhword hash any more.
     *
     * @return string
     */
    protected function formQueryPartForSha512Password(string $password, DatabaseInterface $database, string $userCondition, string $shopCondition): string
    {
        $salt = $database->getOne("SELECT `oxpasssalt` FROM `oxuser` WHERE  1 AND $userCondition $shopCondition");
        if (false !== $salt) {
            $passwordSelect = ' oxuser.oxpassword = ' . $database->quote($this->encodePassword($password, $salt));
        } else {
            $passwordSelect = ' 1 ';
        }

        return $passwordSelect;
    }

    /**
     * @param string            $password
     * @param DatabaseInterface $databaseb
     *
     * @deprecated since v6.4.0 (2019-03-15); `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface`
     *                                        was added as the new default for hashing passwords. Hashing passwords with
     *                                        MD5 and SHA512 is still supported in order support login with older
     *                                        password hashes. Therefor this method might not be
     *                                        compatible with the current passhword hash any more.
     *
     * @return string
     */
    protected function formQueryPartForMD5Password($password, DatabaseInterface $databaseb): string
    {
        $sPassSelect = ' oxuser.oxpassword = BINARY MD5( CONCAT( ' . $databaseb->quote($password) . ', UNHEX( oxuser.oxpasssalt ) ) ) ';

        return $sPassSelect;
    }

    /**
     * @param string            $user
     * @param DatabaseInterface $database
     *
     * @return string
     */
    private function formQueryPartForUserName($user, DatabaseInterface $database): string
    {
        $condition = 'oxuser.oxusername = ' . $database->quote($user);

        return $condition;
    }

    /**
     * Forms shop select query.
     *
     * @param string $sShopID Shop id is used when method is overridden.
     * @param bool   $blAdmin
     *
     * @return string
     */
    protected function formQueryPartForAdminView($sShopID, $blAdmin)
    {
        $sShopSelect = '';

        // Admin view: can only login with higher than 'user' rights
        if ($blAdmin) {
            $sShopSelect = " and ( oxrights != 'user' ) ";
        }

        return $sShopSelect;
    }

    /**
     * @return string
     */
    private function formQueryPartForActiveUser(): string
    {
        $userActiveCondition = 'oxuser.oxactive = 1';

        return $userActiveCondition;
    }

    /**
     * Updates given query. Method is for overriding.
     *
     * @param string $user
     * @param int    $shopId
     *
     * @return string
     */
    protected function formUserCookieQuery($user, $shopId)
    {
        $query = 'select oxid, oxpassword, oxpasssalt from oxuser '
                 . 'where oxuser.oxpassword != "" and  oxuser.oxactive = 1 and oxuser.oxusername = '
                 . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($user);

        return $query;
    }

    private function getRandomToken(): string
    {
        return $this
            ->getContainer()
            ->get(RandomTokenGeneratorBridgeInterface::class)
            ->getAlphanumericToken(32);
    }
}
