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
 * Current user wishlist manager.
 * When user is logged in in this manager window he can modify his
 * own wishlist status - remove articles from wishlist or store
 * them to shopping basket, view detail information. Additionally
 * user can view wishlist of some other user by entering users
 * login name in special field. OXID eShop -> MY ACCOUNT
 *  -> Newsletter.
 */
class Account_Wishlist extends Account
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
     * @var oxlist
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
     * oxuser::GetBasket()), loads similar articles (is available) for
     * the last article in list loaded by oxarticle::GetSimilarProducts() and
     * returns name of template to render account_wishlist::_sThisTemplate
     *
     * @return  string  $_sThisTemplate current template file name
     */
    public function render()
    {
        parent::render();

        // is logged in ?
        $oUser = $this->getUser();
        if ( !$oUser ) {
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
        if ( $this->_blShowSuggest === null ) {
            $this->_blShowSuggest = ( bool ) oxConfig::getParameter( 'blshowsuggest' );
        }
        return $this->_blShowSuggest;
    }

    /**
     * Show the Wishlist
     *
     * @return oxuserbasket | bool
     */
    public function getWishList()
    {
        if ( $this->_oWishList === null ) {
            $this->_oWishList = false;
            if ( $oUser = $this->getUser() ) {
                $this->_oWishList = $oUser->getBasket( 'wishlist' );
                if ( $this->_oWishList->isEmpty() ) {
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
        if ( $this->_aWishProductList === null ) {
            $this->_aWishProductList = false;
            if ( $oWishList = $this->getWishList() ) {
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
        if ( $this->_aSimilarRecommListIds === null ) {
            $this->_aSimilarRecommListIds = false;

            $aWishProdList = $this->getWishProductList();
            if ( is_array( $aWishProdList ) && ( $oSimilarProd = current( $aWishProdList ) ) ) {
                $this->_aSimilarRecommListIds = array( $oSimilarProd->getId() );
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
        $aParams = oxConfig::getParameter( 'editval', true );
        if ( is_array( $aParams ) ) {

            $oParams = ( object ) $aParams;
            $this->setEnteredData( ( object ) oxConfig::getParameter( 'editval' ) );

            if ( !isset( $aParams['rec_name'] ) || !isset( $aParams['rec_email'] ) ||
                 !$aParams['rec_name'] || !$aParams['rec_email'] ) {
                return oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'ERROR_MESSAGE_COMPLETE_FIELDS_CORRECTLY', false, true );
            } else {

                if ( $oUser = $this->getUser() ) {
                    $oParams->send_email = $oUser->oxuser__oxusername->value;
                    $oParams->send_name  = $oUser->oxuser__oxfname->getRawValue().' '.$oUser->oxuser__oxlname->getRawValue();
                    $oParams->send_id    = $oUser->getId();

                    $this->_blEmailSent = oxNew( 'oxemail' )->sendWishlistMail( $oParams );
                    if ( !$this->_blEmailSent ) {
                        return oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'ERROR_MESSAGE_CHECK_EMAIL', false, true );
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
     *
     * @return null
     */
    public function setEnteredData( $oData )
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
        if ( $oUser = $this->getUser() ) {

            $blPublic = (int) oxConfig::getParameter( 'blpublic' );
            $oBasket = $oUser->getBasket( 'wishlist' );
            $oBasket->oxuserbaskets__oxpublic = new oxField( ( $blPublic == 1 ) ? $blPublic : 0 );
            $oBasket->save();
        }
    }

    /**
     * Searches for wishlist of another user. Returns false if no
     * searching conditions set (no login name defined).
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

        $aPath['title'] = oxRegistry::getLang()->translateString( 'MY_ACCOUNT', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = oxRegistry::get("oxSeoEncoder")->getStaticUrl( $this->getViewConfig()->getSelfLink() . 'cl=account' );
        $aPaths[] = $aPath;

        $aPath['title'] = oxRegistry::getLang()->translateString( 'MY_GIFT_REGISTRY', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
