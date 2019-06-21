<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use oxRegistry;
use oxField;

/**
 * Current user wishlist manager.
 * When user is logged in in this manager window he can modify his
 * own wishlist status - remove articles from wishlist or store
 * them to shopping basket, view detail information. Additionally
 * user can view wishlist of some other user by entering users
 * login name in special field. OXID eShop -> MY ACCOUNT
 *  -> Newsletter.
 */
class AccountWishlistController extends \OxidEsales\Eshop\Application\Controller\AccountController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/wishlist.tpl';

    /**
     * If true, list will be shown, if false - will not
     *
     * @var bool
     */
    protected $_blShowSuggest = null;

    /**
     * Wheter the var is false the wishlist will be shown
     *
     * @var wishlist
     */
    protected $_oWishList = null;

    /**
     * list the wishlist items
     *
     * @var wishlist
     */
    protected $_aRecommList = null;

    /**
     * Wheter the var is false the productlist will not be list
     *
     * @var wishlist
     */
    protected $_oEditval = null;

    /**
     * If sending failed give false back
     *
     * @var integer / bool
     */
    protected $_iSendWishList = null;

    /**
     * Wishlist search param
     *
     * @var string
     */
    protected $_sSearchParam = null;

    /**
     * List of users which were found according to search condition
     *
     * @var \OxidEsales\Eshop\Core\Model\ListModel
     */
    protected $_oWishListUsers = false;

    /**
     * Wishlist email sending status
     *
     * @var bool
     */
    protected $_blEmailSent = false;

    /**
     * User entered values for sending email
     *
     * @var array
     */
    protected $_aEditValues = false;

    /**
     * Array of id to form recommendation list.
     *
     * @var array
     */
    protected $_aSimilarRecommListIds = null;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * If user is logged in loads his wishlist articles (articles may be accessed by
     * \OxidEsales\Eshop\Application\Model\User::GetBasket()), loads similar articles (is available) for
     * the last article in list loaded by \OxidEsales\Eshop\Application\Model\Article::GetSimilarProducts() and returns
     * name of template to render \OxidEsales\Eshop\Application\Controller\AccountWishlistController::_sThisTemplate
     *
     * @return  string  $_sThisTemplate current template file name
     */
    public function render()
    {
        parent::render();

        // is logged in ?
        $oUser = $this->getUser();
        if (!$oUser) {
            return $this->_sThisTemplate = $this->_sThisLoginTemplate;
        }

        return $this->_sThisTemplate;
    }

    /**
     * check if the wishlist is allowed
     *
     * @return bool
     */
    public function showSuggest()
    {
        if ($this->_blShowSuggest === null) {
            $this->_blShowSuggest = ( bool ) Registry::getConfig()->getRequestParameter('blshowsuggest');
        }

        return $this->_blShowSuggest;
    }

    /**
     * Show the Wishlist
     *
     * @return \OxidEsales\Eshop\Application\Model\UserBasket | bool
     */
    public function getWishList()
    {
        if ($this->_oWishList === null) {
            $this->_oWishList = false;
            if ($oUser = $this->getUser()) {
                $this->_oWishList = $oUser->getBasket('wishlist');
                if ($this->_oWishList->isEmpty()) {
                    $this->_oWishList = false;
                }
            }
        }

        return $this->_oWishList;
    }

    /**
     * Returns array of producst assigned to user wish list
     *
     * @return array | bool
     */
    public function getWishProductList()
    {
        if ($this->_aWishProductList === null) {
            $this->_aWishProductList = false;
            if ($oWishList = $this->getWishList()) {
                $this->_aWishProductList = $oWishList->getArticles();
            }
        }

        return $this->_aWishProductList;
    }

    /**
     * Return array of id to form recommend list.
     *
     * @return array
     */
    public function getSimilarRecommListIds()
    {
        if ($this->_aSimilarRecommListIds === null) {
            $this->_aSimilarRecommListIds = false;

            $aWishProdList = $this->getWishProductList();
            if (is_array($aWishProdList) && ($oSimilarProd = current($aWishProdList))) {
                $this->_aSimilarRecommListIds = [$oSimilarProd->getId()];
            }
        }

        return $this->_aSimilarRecommListIds;
    }

    /**
     * Sends wishlist mail to recipient. On errors returns false.
     *
     * @return bool
     */
    public function sendWishList()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return false;
        }

        $aParams = Registry::getConfig()->getRequestParameter('editval', true);
        if (is_array($aParams)) {
            $oUtilsView = Registry::getUtilsView();
            $oParams = ( object ) $aParams;
            $this->setEnteredData(( object ) Registry::getConfig()->getRequestParameter('editval'));

            if (!isset($aParams['rec_name']) || !isset($aParams['rec_email']) ||
                !$aParams['rec_name'] || !$aParams['rec_email']
            ) {
                return $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_COMPLETE_FIELDS_CORRECTLY', false, true);
            } else {
                if ($oUser = $this->getUser()) {
                    $sFirstName = 'oxuser__oxfname';
                    $sLastName = 'oxuser__oxlname';
                    $sSendEmail = 'send_email';
                    $sUserNameField = 'oxuser__oxusername';
                    $sSendName = 'send_name';
                    $sSendId = 'send_id';

                    $oParams->$sSendEmail = $oUser->$sUserNameField->value;
                    $oParams->$sSendName = $oUser->$sFirstName->getRawValue() . ' ' . $oUser->$sLastName->getRawValue();
                    $oParams->$sSendId = $oUser->getId();

                    $this->_blEmailSent = oxNew(\OxidEsales\Eshop\Core\Email::class)->sendWishlistMail($oParams);
                    if (!$this->_blEmailSent) {
                        return $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_CHECK_EMAIL', false, true);
                    }
                }
            }
        }
    }

    /**
     * If email was sent.
     *
     * @return bool
     */
    public function isWishListEmailSent()
    {
        return $this->_blEmailSent;
    }

    /**
     * Wishlist data setter
     *
     * @param object $oData suggest data object
     */
    public function setEnteredData($oData)
    {
        $this->_aEditValues = $oData;
    }

    /**
     * Terurns user entered values for sending email.
     *
     * @return array
     */
    public function getEnteredData()
    {
        return $this->_aEditValues;
    }

    /**
     * Changes wishlist status - public/non public. Returns false on
     * error (if user is not logged in).
     *
     * @return bool
     */
    public function togglePublic()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return false;
        }

        if ($oUser = $this->getUser()) {
            $blPublic = (int) Registry::getConfig()->getRequestParameter('blpublic');
            $oBasket = $oUser->getBasket('wishlist');
            $oBasket->oxuserbaskets__oxpublic = new \OxidEsales\Eshop\Core\Field(($blPublic == 1) ? $blPublic : 0);
            $oBasket->save();
        }
    }

    /**
     * Searches for wishlist of another user. Returns false if no
     * searching conditions set (no login name defined).
     */
    public function searchForWishList()
    {
        if ($sSearch = Registry::getConfig()->getRequestParameter('search')) {
            // search for baskets
            $oUserList = oxNew(\OxidEsales\Eshop\Application\Model\UserList::class);
            $oUserList->loadWishlistUsers($sSearch);
            if ($oUserList->count()) {
                $this->_oWishListUsers = $oUserList;
            }

            $this->_sSearchParam = $sSearch;
        }
    }

    /**
     * Returns a list of users which were found according to search condition.
     * If no users were found - false is returned
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel | bool
     */
    public function getWishListUsers()
    {
        return $this->_oWishListUsers;
    }

    /**
     * Returns wish list search parameter
     *
     * @return string
     */
    public function getWishListSearchParam()
    {
        return $this->_sSearchParam;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = [];
        $aPath = [];

        $iBaseLanguage = Registry::getLang()->getBaseLanguage();
        $sSelfLink = $this->getViewConfig()->getSelfLink();

        $aPath['title'] = Registry::getLang()->translateString('MY_ACCOUNT', $iBaseLanguage, false);
        $aPath['link'] = Registry::getSeoEncoder()->getStaticUrl($sSelfLink . 'cl=account');
        $aPaths[] = $aPath;

        $aPath['title'] = Registry::getLang()->translateString('MY_GIFT_REGISTRY', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
