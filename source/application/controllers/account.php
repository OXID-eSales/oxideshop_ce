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
 * Current user "My account" window.
 * When user is logged in arranges "My account" window, by creating
 * links to user details, order review, notice list, wish list. There
 * is a link for logging out. Template includes Topoffer , bargain
 * boxes. OXID eShop -> MY ACCOUNT.
 */


class Account extends oxUBase
{
    /**
     * Number of user's orders.
     * @var integer
     */
    protected $_iOrderCnt = null;

    /**
     * Current article id.
     * @var string
     */
    protected $_sArticleId = null;

    /**
     * Search parameter for Html
     * @var string
     */
    protected $_sSearchParamForHtml = null;

    /**
     * Search parameter
     * @var string
     */
    protected $_sSearchParam = null;

    /**
     * List type
     * @var string
     */
    protected $_sListType = null;

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/account/dashboard.tpl';

    /**
     * Current class login template name.
     * @var string
     */
    protected $_sThisLoginTemplate = 'page/account/login.tpl';

    /**
     * Alternative login template name.
     * @var string
     */
    protected $_sThisAltLoginTemplate = 'page/privatesales/login.tpl';

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Start page meta description CMS ident
     *
     * @var string
     */
    protected $_sMetaDescriptionIdent = 'oxstartmetadescription';

    /**
     * Start page meta keywords CMS ident
     *
     * @var string
     */
    protected $_sMetaKeywordsIdent = 'oxstartmetakeywords';

    /**
     * Sign if to load and show bargain action
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Show tags cloud
     * @var bool
     */
    protected $_blShowTagCloud = false;


    /**
     * Loads action articles. If user is logged and returns name of
     * template to render account::_sThisTemplate
     *
     * @return  string  $_sThisTemplate current template file name
     */
    public function render()
    {
        parent::render();

        // performing redirect if needed
        $this->redirectAfterLogin();

        // is logged in ?
        $oUser = $this->getUser();
        if ( !$oUser || ( $oUser && !$oUser->oxuser__oxpassword->value ) ||
             ( $this->isEnabledPrivateSales() && $oUser && ( !$oUser->isTermsAccepted() || $this->confirmTerms() ) ) ) {
            $this->_sThisTemplate = $this->_getLoginTemplate();
        }

        return $this->_sThisTemplate;
    }

    /**
     * Returns login template name:
     *  - if "login" feature is on returns $this->_sThisAltLoginTemplate
     *  - else returns $this->_sThisLoginTemplate
     *
     * @return string
     */
    protected function _getLoginTemplate()
    {
        return $this->isEnabledPrivateSales() ? $this->_sThisAltLoginTemplate : $this->_sThisLoginTemplate;
    }

    /**
     * Confirms term agreement. Returns value of confirmed term
     *
     * @return string | bool
     */
    public function confirmTerms()
    {
        $blConfirm = oxConfig::getParameter( "term" );
        if ( !$blConfirm && $this->isEnabledPrivateSales() ) {
            $oUser = $this->getUser();
            if ( $oUser && !$oUser->isTermsAccepted() ) {
                $blConfirm = true;
            }
        }

        return $blConfirm;
    }

    /**
     * Returns array from parent::getNavigationParams(). If current request
     * contains "sourcecl" and "anid" parameters - appends array with this
     * data. Array is used to fill forms and append shop urls with actual
     * state parameters
     *
     * @return array
     */
    public function getNavigationParams()
    {
        $aParams = parent::getNavigationParams();

        // source class name
        if ( $sSource = oxConfig::getParameter( "sourcecl" ) ) {
            $aParams['sourcecl'] = $sSource;
        }

        if ( $sSource = oxConfig::getParameter( "anid" ) ) {
            $aParams['anid'] = $sSource;
        }

        return $aParams;
    }

    /**
     * For some user actions (like writing guestbook entry or product
     * review) user must be logged in. So e.g. in product details page
     * there is a link leading to current view. Link contains parameter
     * "sourcecl", which tells where to redirect after successfull login.
     * If this parameter is defined and oxcmp_user::getLoginStatus() ==
     * USER_LOGIN_SUCCESS (means user has just logged in) then user is
     * redirected back to source view.
     *
     * @return null
     */
    public function redirectAfterLogin()
    {
        // in case source class is provided - redirecting back to it with all default parameters
        if ( ( $sSource = oxConfig::getParameter( "sourcecl" ) ) &&
            $this->_oaComponents['oxcmp_user']->getLoginStatus() === USER_LOGIN_SUCCESS ) {

            $sRedirectUrl = $this->getConfig()->getShopUrl().'index.php?cl='.rawurlencode( $sSource );
            // building redirect link

            foreach ( $this->getNavigationParams() as $sName => $sValue ) {
                if ( $sValue && $sName != "sourcecl" ) {
                    $sRedirectUrl .= '&'.rawurlencode( $sName ) . "=" . rawurlencode( $sValue );
                }
            }
            return oxRegistry::getUtils()->redirect( oxRegistry::get("oxUtilsUrl")->processUrl( $sRedirectUrl ), true, 302 );
        }
    }

    /**
     * changes default template for compare in popup
     *
     * @return null
     */
    public function getOrderCnt()
    {
        if ( $this->_iOrderCnt === null ) {
            $this->_iOrderCnt = 0;
            if ( $oUser = $this->getUser() ) {
                $this->_iOrderCnt = $oUser->getOrderCount();
            }
        }
        return $this->_iOrderCnt;
    }

    /**
     * Return the active article id
     *
     * @return string | bool
     */
    public function getArticleId()
    {
        if ( $this->_sArticleId === null) {
            // passing wishlist information
            if ( $sArticleId = oxConfig::getParameter('aid') ) {
                $this->_sArticleId = $sArticleId;
            }
        }
        return $this->_sArticleId;
    }

    /**
     * Template variable getter. Returns search parameter for Html
     *
     * @return string
     */
    public function getSearchParamForHtml()
    {
        if ( $this->_sSearchParamForHtml === null ) {
            $this->_sSearchParamForHtml = false;
            if ( $this->getArticleId() ) {
                $this->_sSearchParamForHtml = oxConfig::getParameter( 'searchparam' );
            }
        }
        return $this->_sSearchParamForHtml;
    }

    /**
     * Template variable getter. Returns search parameter
     *
     * @return string
     */
    public function getSearchParam()
    {
        if ( $this->_sSearchParam === null ) {
            $this->_sSearchParam = false;
            if ( $this->getArticleId() ) {
                $this->_sSearchParam = rawurlencode( oxConfig::getParameter( 'searchparam', true ) );
            }
        }
        return $this->_sSearchParam;
    }

    /**
     * Template variable getter. Returns list type
     *
     * @return string
     */
    public function getListType()
    {
        if ( $this->_sListType === null ) {
            $this->_sListType = false;
            if ( $this->getArticleId() ) {
                // searching in vendor #671
                $this->_sListType = oxConfig::getParameter( 'listtype' );
            }
        }
        return $this->_sListType;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $aPath  = array();
        if ( $oUser = $this->getUser() ) {
            $aPath['title'] = oxRegistry::getLang()->translateString( 'MY_ACCOUNT', oxRegistry::getLang()->getBaseLanguage(), false ) . " - " . $oUser->oxuser__oxusername->value;
        } else {
            $aPath['title'] = oxRegistry::getLang()->translateString( 'LOGIN', oxRegistry::getLang()->getBaseLanguage(), false );
        }
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;
        return $aPaths;
    }

    /**
     * Template variable getter. Returns article list count in comparison
     *
     * @return integer
     */
    public function getCompareItemsCnt()
    {
        $oCompare = oxNew( "compare" );
        $iCompItemsCnt = $oCompare->getCompareItemsCnt();
        return $iCompItemsCnt;
    }
}
