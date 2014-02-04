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
 * View config data access class. Keeps most
 * of getters needed for formatting various urls,
 * config parameters, session information etc.
 */
class oxViewConfig extends oxSuperCfg
{
    /**
     * Active shop object. Can only be accessed when it is assigned
     *
     * @var oxshop
     */
    protected $_oShop = null;

    /**
     * View data array, may only be accedded when it is assigned tohether with shop object
     *
     * @var array
     */
    protected $_aViewData = null;

    /**
     * View config parameters cache array
     *
     * @var array
     */
    protected $_aConfigParams = array();

    /**
     * Help page link
     *
     * @return string
     */
    protected $_sHelpPageLink = null;

    /**
     * returns Country.
     *
     * @var oxcountrylist
     */
    protected $_oCountryList = null;

    /**
     * Trusted shop ratings
     *
     * @var null
     */
    protected $_aTrustedShopRatings = null;

    /**
     * Active theme name
     *
     * @var null
     */
    protected $_sActiveTheme = null;

    /**
     * Shop logo
     * @var string
     */
    protected $_sShopLogo = null;

    /**
     * Returns shops home link
     *
     * @return string
     */
    public function getHomeLink()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'homeLink' ) ) === null ) {
            $myConfig = $this->getConfig();
            $myUtils  = oxRegistry::getUtils();
            $oLang    = oxRegistry::getLang();
            $iLang = $oLang->getBaseLanguage();

            $sValue = null;

            $blAddStartCl = $myUtils->seoIsActive() && ( $iLang != $myConfig->getConfigParam( 'sDefaultLang' ) );


            if ( $blAddStartCl ) {
                $sValue = oxRegistry::get("oxSeoEncoder")->getStaticUrl( $this->getSelfLink() . 'cl=start', $iLang );
                $sValue = oxRegistry::get("oxUtilsUrl")->appendUrl(
                        $sValue,
                        oxRegistry::get("oxUtilsUrl")->getBaseAddUrlParams()
                    );
                $sValue = getStr()->preg_replace('/(\?|&(amp;)?)$/', '', $sValue);
            }

            if ( !$sValue ) {
                $sValue = getStr()->preg_replace('#index.php\??$#', '', $this->getSelfLink());
            }

            $this->setViewConfigParam( 'homeLink', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns active template name (if set)
     *
     * @return string
     */
    public function getActContentLoadId()
    {
        $sTplName = oxRegistry::getConfig()->getRequestParameter( 'oxloadid' );
        // #M1176: Logout from CMS page
        if ( !$sTplName && $this->getConfig()->getTopActiveView() ) {
            $sTplName = $this->getConfig()->getTopActiveView()->getViewConfig()->getViewConfigParam('oxloadid');
        }
        return $sTplName ? basename( $sTplName ) : null;
    }

    /**
     * Returns active manufacturer id
     *
     * @return string
     */
    public function getActTplName()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'tpl' );
    }

    /**
     * Returns active currency id
     *
     * @return string
     */
    public function getActCurrency()
    {
        return $this->getConfig()->getShopCurrency();
    }

    /**
     * Returns shop logout link
     *
     * @return string
     */
    public function getLogoutLink()
    {
        $sClass         = $this->getTopActionClassName();
        $sCatnid        = $this->getActCatId();
        $sMnfid         = $this->getActManufacturerId();
        $sArtnid        = $this->getActArticleId();
        $sTplName       = $this->getActTplName();
        $sContentLoadId = $this->getActContentLoadId();
        $sSearchParam   = $this->getActSearchParam();
        $sSearchTag     = $this->getActSearchTag();
        $sRecommId      = $this->getActRecommendationId();
        $sListType      = $this->getActListType();

        $oConfig = $this->getConfig();
        return ( $oConfig->isSsl()? $oConfig->getShopSecureHomeUrl() : $oConfig->getShopHomeUrl() )
            ."cl={$sClass}"
            . ( $sCatnid ? "&amp;cnid={$sCatnid}" : '' )
            . ( $sArtnid ? "&amp;anid={$sArtnid}" : '' )
            . ( $sMnfid ? "&amp;mnid={$sMnfid}" : '' )
            . ( $sSearchParam ? "&amp;searchparam={$sSearchParam}" : '' )
            . ( $sSearchTag ? "&amp;searchtag={$sSearchTag}" : '' )
            . ( $sRecommId ? "&amp;recommid={$sRecommId}" : '' )
            . ( $sListType ? "&amp;listtype={$sListType}" : '' )
            . "&amp;fnc=logout"
            . ( $sTplName ? "&amp;tpl=".basename( $sTplName ) : '' )
            . ( $sContentLoadId ? "&amp;oxloadid=" . $sContentLoadId : '' )
            . "&amp;redirect=1";
    }

    /**
     * Returns help content link idents
     *
     * @return array
     */
    protected function _getHelpContentIdents()
    {
        $sClass   = $this->getActiveClassName();
        return array( 'oxhelp' . strtolower( $sClass ), 'oxhelpdefault' );
    }

    /**
     * Returns shop help link
     *
     * @return string
     */
    public function getHelpPageLink()
    {
        if ( $this->_sHelpPageLink === null ) {
            $sLink    = false;

            $aContentIdents = $this->_getHelpContentIdents();
            $oContent = oxNew( "oxContent" );
            foreach ( $aContentIdents as $sIdent ) {
                if ( $oContent->loadByIdent( $sIdent ) ) {
                    $sLink = $oContent->getLink();
                    break;
                }
            }

            $this->_sHelpPageLink = $sLink ? $sLink : $this->getHelpLink();
        }
        return $this->_sHelpPageLink;
    }

    /**
     * Returns dynamic shop help link
     *
     * @return string
     */
    public function getHelpLink()
    {
        $sTplName = $this->getActTplName();
        $sClass   = $this->getActiveClassName();
        return $this->getConfig()->getShopCurrentURL()."cl=help&amp;page={$sClass}".( $sTplName ? "&amp;tpl={$sTplName}" : '' );
    }

    /**
     * Returns active category id
     *
     * @return string
     */
    public function getActCatId()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'cnid' );
    }

     /**
     * Returns active article id
     *
     * @return string
     */
    public function getActArticleId()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'anid' );
    }

     /**
     * Returns active search parameter
     *
     * @return string
     */
    public function getActSearchParam()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'searchparam' );
    }

     /**
     * Returns active search tag parameter
     *
     * @return string
     */
    public function getActSearchTag()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'searchtag' );
    }

     /**
     * Returns active recommendation id parameter
     *
     * @return string
     */
    public function getActRecommendationId()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'recommid' );
    }

    /**
     * Returns active listtype parameter
     *
     * @return string
     */
    public function getActListType()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'listtype' );
    }

    /**
     * Returns active manufacturer id
     *
     * @return string
     */
    public function getActManufacturerId()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'mnid' );
    }

     /**
     * Returns active content id
     *
     * @return string
     */
    public function getContentId()
    {
        return oxRegistry::getConfig()->getRequestParameter( 'oxcid' );
    }

    /**
     * Sets view config parameter, which can be accessed in templates in two ways:
     *
     * $oViewConf->getViewConfigParam( $sName )
     *
     * @param string $sName  name of parameter
     * @param mixed  $sValue parameter value
     *
     * @return mixed
     */
    public function setViewConfigParam( $sName, $sValue )
    {
        startProfile('oxviewconfig::setViewConfigParam');

        $this->_aConfigParams[$sName] = $sValue;

        stopProfile('oxviewconfig::setViewConfigParam');
    }

    /**
     * Returns current view config parameter
     *
     * @param string $sName name of parameter to get
     *
     * @return mixed
     */
    public function getViewConfigParam( $sName )
    {
        startProfile('oxviewconfig::getViewConfigParam');

        if ( $this->_oShop && isset( $this->_oShop->$sName ) ) {
            $sValue = $this->_oShop->$sName;
        } elseif ( $this->_aViewData && isset( $this->_aViewData[ $sName ] ) ) {
            $sValue = $this->_aViewData[ $sName ];
        } else {
            $sValue = ( isset( $this->_aConfigParams[ $sName ] ) ? $this->_aConfigParams[ $sName ] : null );
        }

        stopProfile('oxviewconfig::getViewConfigParam');

        return $sValue;
    }

    /**
     * Sets shop object and view data to view config. This is needed mostly for
     * old templates
     *
     * @param oxshop $oShop     shop object
     * @param array  $aViewData view data array
     *
     * @return null
     */
    public function setViewShop( $oShop, $aViewData )
    {
        $this->_oShop     = $oShop;
        $this->_aViewData = $aViewData;
    }

    /**
     * Returns session id
     *
     * @deprecated v5.1.0 Use conditional sid getter oxView::getSidForWidget() for widgets instead
     *
     * @return string
     */
    public function getSessionId()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'sessionid' ) ) === null ) {
            $sValue = $this->getSession()->getId();
            $this->setViewConfigParam( 'sessionid', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns forms hidden session parameters
     *
     * @return string
     */
    public function getHiddenSid()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'hiddensid' ) ) === null ) {
            $sValue = $this->getSession()->hiddenSid();

            // appending language info to form
            if ( ( $sLang = oxRegistry::getLang()->getFormLang() ) ) {
                $sValue .= "\n{$sLang}";
            }


            $this->setViewConfigParam( 'hiddensid', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops self link
     *
     * @return string
     */
    public function getSelfLink()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'selflink' ) ) === null ) {
            $sValue = $this->getConfig()->getShopHomeURL();
            $this->setViewConfigParam( 'selflink', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops self ssl link
     *
     * @return string
     */
    public function getSslSelfLink()
    {
        if ( $this->isAdmin() ) {
            // using getSelfLink() method in admin mode (#2745)
            return $this->getSelfLink();
        }

        if ( ( $sValue = $this->getViewConfigParam( 'sslselflink' ) ) === null ) {
            $sValue = $this->getConfig()->getShopSecureHomeURL();
            $this->setViewConfigParam( 'sslselflink', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops base directory path
     *
     * @return string
     */
    public function getBaseDir()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'basedir' ) ) === null ) {

            if ( $this->getConfig()->isSsl() ) {
                $sValue = $this->getConfig()->getSSLShopURL();
            } else {
                $sValue = $this->getConfig()->getShopURL();
            }

            $this->setViewConfigParam( 'basedir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops utility directory path
     *
     * @return string
     */
    public function getCoreUtilsDir()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'coreutilsdir' ) ) === null ) {
            $sValue = $this->getConfig()->getCoreUtilsURL();
            $this->setViewConfigParam( 'coreutilsdir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops action link
     *
     * @return string
     */
    public function getSelfActionLink()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'selfactionlink' ) ) === null ) {
            $sValue = $this->getConfig()->getShopCurrentUrl();
            $this->setViewConfigParam( 'selfactionlink', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops home path
     *
     * @return string
     */
    public function getCurrentHomeDir()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'currenthomedir' ) ) === null ) {
            $sValue = $this->getConfig()->getCurrentShopUrl();
            $this->setViewConfigParam( 'currenthomedir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops basket link
     *
     * @return string
     */
    public function getBasketLink()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'basketlink' ) ) === null ) {
            $sValue = $this->getConfig()->getShopHomeURL()   . 'cl=basket';
            $this->setViewConfigParam( 'basketlink', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops order link
     *
     * @return string
     */
    public function getOrderLink()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'orderlink' ) ) === null ) {
            $sValue = $this->getConfig()->getShopSecureHomeUrl() . 'cl=user';
            $this->setViewConfigParam( 'orderlink', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops payment link
     *
     * @return string
     */
    public function getPaymentLink()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'paymentlink' ) ) === null ) {
            $sValue = $this->getConfig()->getShopSecureHomeUrl() . 'cl=payment';
            $this->setViewConfigParam( 'paymentlink', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops order execution link
     *
     * @return string
     */
    public function getExeOrderLink()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'exeorderlink' ) ) === null ) {
            $sValue = $this->getConfig()->getShopSecureHomeUrl() . 'cl=order&amp;fnc=execute';
            $this->setViewConfigParam( 'exeorderlink', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops order confirmation link
     *
     * @return string
     */
    public function getOrderConfirmLink()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'orderconfirmlink' ) ) === null ) {
            $sValue = $this->getConfig()->getShopSecureHomeUrl() . 'cl=order';
            $this->setViewConfigParam( 'orderconfirmlink', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops resource url
     *
     * @param string $sFile resource file name
     *
     * @return string
     */
    public function getResourceUrl( $sFile = null )
    {
        if ( $sFile ) {
            $sValue = $this->getConfig()->getResourceUrl( $sFile, $this->isAdmin() );
        } elseif ( ( $sValue = $this->getViewConfigParam( 'basetpldir' ) ) === null ) {
            $sValue = $this->getConfig()->getResourceUrl( '', $this->isAdmin() );
            $this->setViewConfigParam( 'basetpldir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops current (related to language) templates path
     *
     * @return string
     */
    public function getTemplateDir()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'templatedir' ) ) === null ) {
            $sValue = $this->getConfig()->getTemplateDir( $this->isAdmin() );
            $this->setViewConfigParam( 'templatedir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns shops current templates url
     *
     * @return string
     */
    public function getUrlTemplateDir()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'urltemplatedir' ) ) === null ) {
            $sValue = $this->getConfig()->getTemplateUrl( $this->isAdmin() );
            $this->setViewConfigParam( 'urltemplatedir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns image url
     *
     * @param string $sFile Image file name
     * @param bool   $bSsl  Whether to force SSL
     *
     * @return string
     */
    public function getImageUrl( $sFile = null, $bSsl = null )
    {
        if ($sFile) {
           $sValue = $this->getConfig()->getImageUrl( $this->isAdmin(), $bSsl, null, $sFile );
        } elseif ( ( $sValue = $this->getViewConfigParam( 'imagedir' ) ) === null ) {
            $sValue = $this->getConfig()->getImageUrl( $this->isAdmin(), $bSsl );
            $this->setViewConfigParam( 'imagedir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns non ssl image url
     *
     * @return string
     */
    public function getNoSslImageDir()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'nossl_imagedir' ) ) === null ) {
            $sValue = $this->getConfig()->getImageUrl( $this->isAdmin(), false );
            $this->setViewConfigParam( 'nossl_imagedir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns dynamic (language related) image url
     * Left for compatibility reasons for a while. Will be removed in future
     *
     * @return string
     */
    public function getPictureDir()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'picturedir' ) ) === null ) {
            $sValue = $this->getConfig()->getPictureUrl( null, $this->isAdmin() );
            $this->setViewConfigParam( 'picturedir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns admin path
     *
     * @return string
     */
    public function getAdminDir()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'sAdminDir' ) ) === null ) {
            $sValue = $this->getConfig()->getConfigParam( 'sAdminDir' );
            $this->setViewConfigParam( 'sAdminDir', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns currently open shop id
     *
     * @return string
     */
    public function getActiveShopId()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'shopid' ) ) === null ) {
            $sValue = $this->getConfig()->getShopId();
            $this->setViewConfigParam( 'shopid', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns ssl mode (on/off)
     *
     * @return string
     */
    public function isSsl()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'isssl' ) ) === null ) {
            $sValue = $this->getConfig()->isSsl();
            $this->setViewConfigParam( 'isssl', $sValue );
        }
        return $sValue;
    }


    /**
     * Returns visitor ip address
     *
     * @return string
     */
    public function getRemoteAddress()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'ip' ) ) === null ) {
            $sValue = oxRegistry::get("oxUtilsServer")->getRemoteAddress();
            $this->setViewConfigParam( 'ip', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns basket popup identifier
     *
     * @return string
     */
    public function getPopupIdent()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'popupident' ) ) === null ) {
            $sValue = md5( $this->getConfig()->getShopUrl() );
            $this->setViewConfigParam( 'popupident', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns random basket popup identifier
     *
     * @return string
     */
    public function getPopupIdentRand()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'popupidentrand' ) ) === null ) {
            $sValue = md5( time() );
            $this->setViewConfigParam( 'popupidentrand', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns list view paging url
     *
     * @return string
     */
    public function getArtPerPageForm()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'artperpageform' ) ) === null ) {
            $sValue = $this->getConfig()->getShopCurrentUrl();
            $this->setViewConfigParam( 'artperpageform', $sValue );
        }
        return $sValue;
    }

    /**
     * Returns "blVariantParentBuyable" parent article config state
     *
     * @return string
     */
    public function isBuyableParent()
    {
        return $this->getConfig()->getConfigParam( 'blVariantParentBuyable' );
    }

    /**
     * Returns config param "blShowBirthdayFields" value
     *
     * @return string
     */
    public function showBirthdayFields()
    {
        return $this->getConfig()->getConfigParam( 'blShowBirthdayFields' );
    }

    /**
     * Returns config param "blShowFinalStep" value
     *
     * @deprecated since 2012-11-19. Option blShowFinalStep is removed
     *
     * @return bool
     */
    public function showFinalStep()
    {
        return true;
    }

    /**
     * Returns config param "aNrofCatArticles" value
     *
     * @return string
     */
    public function getNrOfCatArticles()
    {
        // checking if all needed data is set
        switch (oxRegistry::getSession()->getVariable( 'ldtype' )) {
            case 'grid':
                return $this->getConfig()->getConfigParam( 'aNrofCatArticlesInGrid' );
                break;
            case 'line':
            case 'infogrid':
            default:
                return $this->getConfig()->getConfigParam( 'aNrofCatArticles' );
        }
    }

    /**
     * Returns config param "bl_showWishlist" value
     *
     * @return bool
     */
    public function getShowWishlist()
    {
        return $this->getConfig()->getConfigParam( 'bl_showWishlist' );
    }

    /**
     * Returns config param "bl_showCompareList" value
     *
     * @return bool
     */
    public function getShowCompareList()
    {
        $myConfig = $this->getConfig();
        $blShowCompareList = true;

        if ( !$myConfig->getConfigParam( 'bl_showCompareList' ) ||
            ( $myConfig->getConfigParam( 'blDisableNavBars' ) && $myConfig->getActiveView()->getIsOrderStep() ) ) {
            $blShowCompareList = false;
        }

        return $blShowCompareList;
    }

    /**
     * Returns config param "bl_showListmania" value
     *
     * @return bool
     */
    public function getShowListmania()
    {
        return $this->getConfig()->getConfigParam( 'bl_showListmania' );
    }

    /**
     * Returns config param "bl_showVouchers" value
     *
     * @return bool
     */
    public function getShowVouchers()
    {
        return $this->getConfig()->getConfigParam( 'bl_showVouchers' );
    }

    /**
     * Returns config param "bl_showGiftWrapping" value
     *
     * @return bool
     */
    public function getShowGiftWrapping()
    {
        return $this->getConfig()->getConfigParam( 'bl_showGiftWrapping' );
    }

    /**
     * Returns session language id
     *
     * @return string
     */
    public function getActLanguageId()
    {
        if ( ( $sValue = $this->getViewConfigParam( 'lang' ) ) === null ) {
            $iLang = oxRegistry::getConfig()->getRequestParameter( 'lang' );
            $sValue = ( $iLang !== null ) ? $iLang : oxRegistry::getLang()->getBaseLanguage();
            $this->setViewConfigParam( 'lang', $sValue );
        }
        return $sValue;
    }

     /**
     * Returns session language id
     *
     * @return string
     */
    public function getActLanguageAbbr()
    {
        return oxRegistry::getLang()->getLanguageAbbr( $this->getActLanguageId() );
    }

    /**
     * Returns name of active view class
     *
     * @return string
     */
    public function getActiveClassName()
    {
        return $this->getConfig()->getActiveView()->getClassName();
    }

    /**
     * Returns name of a class of top view in the chain
     * (given a generic fnc, e.g. logout)
     *
     * @return string
     */
    public function getTopActiveClassName()
    {
        return $this->getConfig()->getTopActiveView()->getClassName();
    }

    /**
     * Returns max number of items shown on page
     *
     * @return int
     */
    public function getArtPerPageCount()
    {
        return $this->getViewConfigParam( 'iartPerPage' );
    }

    /**
     * Returns navigation url parameters
     *
     * @return string
     */
    public function getNavUrlParams()
    {
        if ( ( $sParams = $this->getViewConfigParam( 'navurlparams' ) ) === null ) {
            $sParams = '';
            $aNavParams = $this->getConfig()->getActiveView()->getNavigationParams();
            foreach ( $aNavParams as $sName => $sValue ) {
                if ( isset( $sValue ) ) {
                    if ( $sParams ) {
                        $sParams .= '&amp;';
                    }
                    $sParams .= "{$sName}=".rawurlencode( $sValue );
                }
            }
            if ( $sParams ) {
                $sParams = '&amp;'.$sParams;
            }
            $this->setViewConfigParam( 'navurlparams', $sParams );
        }

        return $sParams;
    }

    /**
     * Returns navigation forms parameters
     *
     * @return string
     */
    public function getNavFormParams()
    {

        if ( ( $sParams = $this->getViewConfigParam( 'navformparams' ) ) === null ) {
            $oStr = getStr();
            $sParams = '';
            $aNavParams = $this->getConfig()->getTopActiveView()->getNavigationParams();
            foreach ( $aNavParams as $sName => $sValue ) {
                if ( isset( $sValue ) ) {
                    $sParams .= "<input type=\"hidden\" name=\"{$sName}\" value=\"".$oStr->htmlentities( $sValue )."\" />\n";
                }
            }
            $this->setViewConfigParam( 'navformparams', $sParams );
        }
        return $sParams;
    }

    /**
     * Returns config param "blStockOnDefaultMessage" value
     *
     * @return string
     */
    public function getStockOnDefaultMessage()
    {
        return $this->getConfig()->getConfigParam( 'blStockOnDefaultMessage' );
    }

    /**
     * Returns config param "blStockOnDefaultMessage" value
     *
     * @return string
     */
    public function getStockOffDefaultMessage()
    {
        return $this->getConfig()->getConfigParam( 'blStockOffDefaultMessage' );
    }

    /**
     * Returns shop version defined in view
     *
     * @return string
     */
    public function getShopVersion()
    {
        return $this->getViewConfigParam( 'sShopVersion' );
    }

    /**
     * Returns AJAX request url
     *
     * @return  string
     */
    public function getAjaxLink()
    {
        return $this->getViewConfigParam( 'ajaxlink' );
    }

    /**
     * Returns multishop status
     *
     * @return bool
     */
    public function isMultiShop()
    {
        $oShop = $this->getConfig()->getActiveShop();
        return isset( $oShop->oxshops__oxismultishop ) ? ( (bool) $oShop->oxshops__oxismultishop->value ) : false;
    }

    /**
     * Returns service url
     *
     * @return string
     */
    public function getServiceUrl()
    {
        return $this->getViewConfigParam( 'sServiceUrl' );
    }

    /**
     * Returns session Remote Access token. Later you can pass the token over rtoken URL param
     * when you want to access the shop, for example, from different client.
     *
     * @return string
     */
    public function getRemoteAccessToken()
    {
        $sRaToken = oxRegistry::getSession()->getRemoteAccessToken();

        return $sRaToken;
    }

    /**
     * Returns name of a view class, which will be active for an action
     * (given a generic fnc, e.g. logout)
     *
     * @return string
     */
    public function getActionClassName()
    {
        return $this->getConfig()->getActiveView()->getActionClassName();
    }

    /**
     * Returns name of a class of top view in the chain
     * (given a generic fnc, e.g. logout)
     *
     * @return string
     */
    public function getTopActionClassName()
    {
        return $this->getConfig()->getTopActiveView()->getActionClassName();
    }

    /**
     * Returns facebook application key value
     *
     * @return string
     */
    public function getFbAppId()
    {
        return $this->getConfig()->getConfigParam( 'sFbAppId' );
    }

    /**
     * should basket timeout counter be shown?
     *
     * @return bool
     */
    public function getShowBasketTimeout()
    {
        return $this->getConfig()->getConfigParam( 'blPsBasketReservationEnabled' )
            && ($this->getSession()->getBasketReservations()->getTimeLeft() > 0);
    }

    /**
     * return the seconds left until basket expiration
     *
     * @return int
     */
    public function getBasketTimeLeft()
    {
        if (!isset($this->_dBasketTimeLeft)) {
            $this->_dBasketTimeLeft = $this->getSession()->getBasketReservations()->getTimeLeft();
        }
        return $this->_dBasketTimeLeft;
    }

    /**
     * Checks if Facebook connect is on. If yes, also checks if Facebook application id
     * and secure key are entered in config table.
     *
     * @return bool
     */
    public function getShowFbConnect()
    {
        $myConfig = $this->getConfig();

        if ( $myConfig->getConfigParam( 'bl_showFbConnect' ) ) {
            if ( $myConfig->getConfigParam( "sFbAppId" ) && $myConfig->getConfigParam( "sFbSecretKey" ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns Trusted shops domain name (includes "http://")
     *
     * @return string
     */
    public function getTsDomain()
    {
        $sDomain = false;
        $aTsConfig = $this->getConfig()->getConfigParam( "aTsConfig" );
        if ( is_array( $aTsConfig ) ) {
            $sDomain = $aTsConfig["blTestMode"] ? $aTsConfig["sTsTestUrl"] : $aTsConfig["sTsUrl"];
        }
        return $sDomain;
    }

    /**
     * Returns Trusted Shops Widget image url
     *
     * @deprecated since v4.7.6/5.0.6 (2013-07-09); new Trusted Shops badge added
     *
     * @return string
     */
    public function getTsWidgetUrl()
    {
        $sUrl = false;
        if ( $sTsId = $this->getTsId() ) {
            $sTsUrl = $this->getTsDomain();

            $aTsConfig = $this->getConfig()->getConfigParam( "aTsConfig" );
            $sTsWidgetUri = isset( $aTsConfig["sTsWidgetUri"] ) ? current( $aTsConfig["sTsWidgetUri"] ) : false;

            if ( $sTsUrl && $sTsWidgetUri ) {
                //$sLocal = $this->getConfig()->getImageDir()."{$sTsId}.gif";
                $sUrl = sprintf( "{$sTsUrl}/{$sTsWidgetUri}", $sTsId );
                //if ( $sImgName = oxRegistry::getUtils()->getRemoteCachePath( $sUrl, $sLocal ) ) {
                //    $sUrl = $this->getImageUrl().basename( $sImgName );
                //}
            }
        }

        return $sUrl;
    }

    /**
     * Trusted Shops widget info url
     *
     * @deprecated since v4.7.6/5.0.6 (2013-07-09); new Trusted Shops badge added
     *
     * @return string | bool
     */
    public function getTsInfoUrl()
    {
        $sUrl = false;
        if ( $sTsId = $this->getTsId() ) {
            $sTsUrl = $this->getTsDomain();

            $sLangId = oxRegistry::getLang()->getLanguageAbbr();
            $aTsConfig = $this->getConfig()->getConfigParam( "aTsConfig" );
            $sTsInfoUri = ( isset( $aTsConfig["sTsInfoUri"] ) && isset( $aTsConfig["sTsInfoUri"][$sLangId] ) ) ? $aTsConfig["sTsInfoUri"][$sLangId] : false;

            if ( $sTsUrl && $sTsInfoUri ) {
                $sUrl = sprintf( "{$sTsUrl}/{$sTsInfoUri}", $sTsId );
            }
        }

        return $sUrl;
    }

    /**
     * Gets Trusted shops ratings from Trusted shops
     *
     * @return array
     */
    public function getTsRatings()
    {
        if ( $this->_aTrustedShopRatings === null ) {
            if ( $sTsId = $this->getTsId() ) {
                $oTsRatings = oxNew( "oxTsRatings" );
                $oTsRatings->setTsId( $sTsId );
                $this->_aTrustedShopRatings = $oTsRatings->getRatings();

                return $this->_aTrustedShopRatings;
            }
        }
        return $this->_aTrustedShopRatings;
    }

    /**
     * Trusted Shops ratings url
     *
     * @return string | bool
     */
    public function getTsRatingUrl()
    {
        $sUrl = false;
        if ( $sTsId = $this->getTsId() ) {
            $sTsUrl = $this->getTsDomain();

            $sLangId = oxRegistry::getLang()->getLanguageAbbr();
            $aTsConfig = $this->getConfig()->getConfigParam( "aTsConfig" );
            $sTsRateUri = ( isset( $aTsConfig["sTsRatingUri"] ) && isset( $aTsConfig["sTsRatingUri"][$sLangId] ) ) ? $aTsConfig["sTsRatingUri"][$sLangId] : false;

            if ( $sTsUrl && $sTsRateUri ) {
                $sUrl = sprintf( "{$sTsUrl}/{$sTsRateUri}", $sTsId );
            }
        }

        return $sUrl;
    }

    /**
     * Returns true if Trusted Shops feature is On
     *
     * @param string $sType type of element to check
     *
     * @return bool
     */
    public function showTs( $sType )
    {
        $blShow = false;
        switch ( $sType ) {
            case "WIDGET":
                $blShow = (bool) $this->getConfig()->getConfigParam( "blTsWidget" );
                break;
            case "THANKYOU":
                $blShow = (bool) $this->getConfig()->getConfigParam( "blTsThankyouReview" );
                break;
            case "ORDEREMAIL":
                $blShow = (bool) $this->getConfig()->getConfigParam( "blTsOrderEmailReview" );
                break;
            case "ORDERCONFEMAIL":
                $blShow = (bool) $this->getConfig()->getConfigParam( "blTsOrderSendEmailReview" );
                break;
        }
        return $blShow;
    }

    /**
     * Returns Trusted Shops id
     *
     * @return string
     */
    public function getTsId()
    {
        $sTsId = false;
        $oConfig = $this->getConfig();
        $aLangIds = $oConfig->getConfigParam( "aTsLangIds" );
        $aActInfo = $oConfig->getConfigParam( "aTsActiveLangIds" );

        // mapping with language id
        $sLangId = oxRegistry::getLang()->getLanguageAbbr();
        if ( isset( $aActInfo[$sLangId] ) && $aActInfo[$sLangId] &&
             isset( $aLangIds[$sLangId] ) && $aLangIds[$sLangId]
           ) {
            $sTsId = $aLangIds[$sLangId];
        }

        return $sTsId;
    }

    /**
     * true if blocks javascript code be enabled in templates
     *
     * @return bool
     */
    public function isTplBlocksDebugMode()
    {
        return (bool) $this->getConfig()->getConfigParam('blDebugTemplateBlocks');
    }

    /**
     * min length of password
     *
     * @return int
     */
    public function getPasswordLength()
    {
        $iPasswordLength = 6;

        $oConfig = $this->getConfig();

        if ($oConfig->getConfigParam( "iPasswordLength" ) ) {
            $iPasswordLength = $oConfig->getConfigParam( "iPasswordLength" );
        }

        return $iPasswordLength;
    }

    /**
     * Return country list
     *
     * @return oxcountrylist
     */
    public function getCountryList()
    {
        if ( $this->_oCountryList === null ) {
            // passing country list
            $this->_oCountryList = oxNew( 'oxcountrylist' );
            $this->_oCountryList->loadActiveCountries();
        }
        return $this->_oCountryList;
    }


    /**
     * return path to the requested module file
     *
     * @param string $sModule module name (directory name in modules dir)
     * @param string $sFile   file name to lookup
     *
     * @throws oxFileException
     *
     * @return string
     */
    public function getModulePath($sModule, $sFile = '')
    {
        if (!$sFile || ($sFile[0] != '/')) {
            $sFile = '/'.$sFile;
        }
        $oModule = oxNew("oxmodule");
        $sModulePath = $oModule->getModulePath($sModule);
        $sFile = $this->getConfig()->getModulesDir().$sModulePath.$sFile;
        if (file_exists($sFile) || is_dir($sFile)) {
            return $sFile;
        } else {
            $oEx = oxNew( "oxFileException", "Requested file not found for module $sModule ($sFile)" );
            $oEx->debugOut();
            if (!$this->getConfig()->getConfigParam( 'iDebug' )) {
                return '';
            }
            throw $oEx;
        }
    }

    /**
     * return url to the requested module file
     *
     * @param string $sModule module name (directory name in modules dir)
     * @param string $sFile   file name to lookup
     *
     * @throws oxFileException
     *
     * @return string
     */
    public function getModuleUrl($sModule, $sFile = '')
    {
        $sUrl = str_replace(
                    rtrim($this->getConfig()->getConfigParam('sShopDir'), '/'),
                    rtrim($this->getConfig()->getCurrentShopUrl( false ), '/'),
                    $this->getModulePath($sModule, $sFile)
                           );
        return $sUrl;
    }

    /**
     * return param value
     *
     * @param string $sName param name
     *
     * @return mix
     */
    public function getViewThemeParam( $sName )
    {
        $sValue = false;

        if ($this->getConfig()->isThemeOption( $sName ) ) {
            $sValue = $this->getConfig()->getConfigParam( $sName );
        }

        return $sValue;
    }


    /**
     * Returns true if selection lists must be displayed in details page
     *
     * @return bool
     */
    public function showSelectLists()
    {
        return (bool) $this->getConfig()->getConfigParam( 'bl_perfLoadSelectLists' );
    }

    /**
     * Returns true if selection lists must be displayed in details page
     *
     * @return bool
     */
    public function showSelectListsInList()
    {
        return $this->showSelectLists() && (bool) $this->getConfig()->getConfigParam( 'bl_perfLoadSelectListsInAList' );
    }



    /**
     * Checks if alternative image server is configured.
     *
     * @return bool
     */
    public function isAltImageServerConfigured()
    {
        $oConfig = $this->getConfig();

        return $oConfig->getConfigParam('sAltImageUrl') || $oConfig->getConfigParam('sSSLAltImageUrl') ||
               $oConfig->getConfigParam('sAltImageDir') || $oConfig->getConfigParam('sSSLAltImageDir');
    }

    /**
     * Get config parameter for view to check if functionality is turned on or off.
     *
     * @param string $sParamName config parameter name.
     *
     * @return bool
     */
    public function isFunctionalityEnabled( $sParamName )
    {
        return (bool) $this->getConfig()->getConfigParam( $sParamName );
    }

    /**
     * Returns active theme name
     *
     * @return string
     */
    public function getActiveTheme()
    {
        if ( $this->_sActiveTheme === null ) {
            $oTheme = oxNew( 'oxTheme' );
            $this->_sActiveTheme = $oTheme->getActiveThemeId();
        }
        return $this->_sActiveTheme;
    }

    /**
     * Returns shop logo image file name from config option
     *
     * @return string
     */
    public function getShopLogo()
    {
        if ( is_null( $this->_sShopLogo ) ) {

            $sLogoImage = $this->getConfig()->getConfigParam( 'sShopLogo' );
            if ( empty( $sLogoImage ) )
            {
                $sLogoImage = "logo.png";
            }

            $this->setShopLogo( $sLogoImage );
        }

        return $this->_sShopLogo;
    }

    /**
     * Sets shop logo
     *
     * @param string $sLogo shop logo image file name
     *
     * @return null
     */
    public function setShopLogo( $sLogo )
    {
        $this->_sShopLogo = $sLogo;
    }
}
