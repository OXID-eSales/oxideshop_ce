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
 * The wishlist of someone else is displayed.
 */
class Wishlist extends oxUBase
{

    /**
     * Current class template name.
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
     * @var oxlist
     */
    protected $_oWishListUsers = false;

    /**
     * Sign if to load and show bargain action
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

        if ( $this->_oWishUser === null) {
            $this->_oWishUser = false;

            $sUserId = oxConfig::getParameter( 'wishid') ? oxConfig::getParameter( 'wishid' ): oxSession::getVar( 'wishid');
            if ( $sUserId ) {
                $oUser = oxNew( 'oxuser' );
                if ( $oUser->load( $sUserId ) ) {

                    // passing wishlist information
                    $this->_oWishUser = $oUser;

                    // store this one to session
                    oxSession::setVar( 'wishid', $sUserId );
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
        if ( $this->_oWishList === null) {
            $this->_oWishList = false;

            // passing wishlist information
            if ( $oUser = $this->getWishUser() ) {

                $oWishlistBasket = $oUser->getBasket( 'wishlist' );
                $this->_oWishList = $oWishlistBasket->getArticles();

                if (!$oWishlistBasket->isVisible())
                    $this->_oWishList = false;


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
     * @return bool
     */
    public function searchForWishList()
    {
        if ( $sSearch = oxConfig::getParameter( 'search' ) ) {

            // search for baskets
            $oUserList = oxNew( 'oxuserlist' );
            $oUserList->loadWishlistUsers( $sSearch );
            if ( $oUserList->count() ) {
                $this->_oWishListUsers = $oUserList;
            }
            $this->_sSearchParam = $sSearch;
        }
    }

    /**
     * Returns a list of users which were found according to search condition.
     * If no users were found - false is returned
     *
     * @return oxlist | bool
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
        $aPaths = array();
        $aPath = array();

        $aPath['title'] = oxRegistry::getLang()->translateString( 'PUBLIC_GIFT_REGISTRIES', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
