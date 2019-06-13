<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use oxRegistry;
use oxUBase;
use oxList;

/**
 * The wishlist of someone else is displayed.
 */
class WishListController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/wishlist/wishlist.tpl';

    /**
     * user object list
     *
     * @return object
     */
    protected $_oWishUser = null;

    /**
     * wishlist object list
     *
     * @return object
     */
    protected $_oWishList = null;

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
     * Sign if to load and show bargain action
     *
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * return the user which is owner of the wish list
     *
     * @return object | bool
     */
    public function getWishUser()
    {
        if ($this->_oWishUser === null) {
            $this->_oWishUser = false;

            $sWishIdParameter = Registry::getConfig()->getRequestParameter('wishid');
            $sUserId = $sWishIdParameter ? $sWishIdParameter : Registry::getSession()->getVariable('wishid');
            if ($sUserId) {
                $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
                if ($oUser->load($sUserId)) {
                    // passing wishlist information
                    $this->_oWishUser = $oUser;

                    // store this one to session
                    Registry::getSession()->setVariable('wishid', $sUserId);
                }
            }
        }

        return $this->_oWishUser;
    }

    /**
     * return the articles which are in the wish list
     *
     * @return object | bool
     */
    public function getWishList()
    {
        if ($this->_oWishList === null) {
            $this->_oWishList = false;

            // passing wishlist information
            if ($oUser = $this->getWishUser()) {
                $oWishlistBasket = $oUser->getBasket('wishlist');
                $this->_oWishList = $oWishlistBasket->getArticles();

                if (!$oWishlistBasket->isVisible()) {
                    $this->_oWishList = false;
                }
            }
        }

        return $this->_oWishList;
    }

    /**
     * Searches for wishlist of another user. Returns false if no
     * searching conditions set (no login name defined).
     *
     * Template variables:
     * <b>wish_result</b>, <b>search</b>
     *
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
        $aPath['title'] = Registry::getLang()->translateString('PUBLIC_GIFT_REGISTRIES', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Page title
     *
     * @return string
     */
    public function getTitle()
    {
        $oLang = Registry::getLang();
        if ($oUser = $this->getWishUser()) {
            $sTranslatedString = $oLang->translateString('GIFT_REGISTRY_OF_3', $oLang->getBaseLanguage(), false);
            $sFirstnameField = 'oxuser__oxfname';
            $sLastnameField = 'oxuser__oxlname';

            return $sTranslatedString . ' ' . $oUser->$sFirstnameField->value . ' ' . $oUser->$sLastnameField->value;
        }

        return $oLang->translateString('PUBLIC_GIFT_REGISTRIES', $oLang->getBaseLanguage(), false);
    }
}
