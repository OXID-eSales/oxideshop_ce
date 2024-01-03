<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Exception\FileException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\EshopCommunity\Core\Exception\ShopException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModuleAssetsPathResolverBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\Facts\Facts;

/**
 * View config data access class. Keeps most
 * of getters needed for formatting various urls,
 * config parameters, session information etc.
 */
class ViewConfig extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Active shop object. Can only be accessed when it is assigned
     *
     * @var \OxidEsales\Eshop\Application\Model\Shop
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
    protected $_aConfigParams = [];

    /**
     * Help page link
     *
     * @return string
     */
    protected $_sHelpPageLink = null;

    /**
     * @var \OxidEsales\Eshop\Application\Model\CountryList
     */
    protected $_oCountryList = null;

    /**
     * Active theme name
     *
     * @var null
     */
    protected $_sActiveTheme = null;

    /**
     * Shop logo
     *
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
        if (($sValue = $this->getViewConfigParam('homeLink')) === null) {
            $sValue = null;

            $blAddStartCl = $this->isStartClassRequired();
            if ($blAddStartCl) {
                $baseLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
                $sValue = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->getStaticUrl($this->getSelfLink() . 'cl=start', $baseLanguage);
                $sValue = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->appendUrl(
                    $sValue,
                    \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->getBaseAddUrlParams()
                );
                $sValue = Str::getStr()->preg_replace('/(\?|&(amp;)?)$/', '', $sValue);
            }

            if (!$sValue) {
                $sValue = Str::getStr()->preg_replace('#index.php\??$#', '', $this->getSelfLink());
            }

            $this->setViewConfigParam('homeLink', $sValue);
        }

        return $sValue;
    }

    /**
     * Check if some shop selection page must be shown
     *
     * @return bool
     */
    protected function isStartClassRequired()
    {
        $baseLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $shopConfig = Registry::getConfig();
        $isSeoActive = \OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive();

        return $isSeoActive && ($baseLanguage != $shopConfig->getConfigParam('sDefaultLang'));
    }

    /**
     * Returns active template name (if set)
     *
     * @return string
     */
    public function getActContentLoadId()
    {
        $sTplName = Registry::getRequest()->getRequestEscapedParameter('oxloadid');
        // #M1176: Logout from CMS page
        if (!$sTplName && Registry::getConfig()->getTopActiveView()) {
            $sTplName = Registry::getConfig()->getTopActiveView()->getViewConfig()->getViewConfigParam('oxloadid');
        }

        return $sTplName ? basename($sTplName) : null;
    }

    /**
     * Returns active manufacturer id
     *
     * @return string
     */
    public function getActTplName()
    {
        return Registry::getRequest()->getRequestEscapedParameter('tpl');
    }

    /**
     * Returns active currency id
     *
     * @return string
     */
    public function getActCurrency()
    {
        return Registry::getConfig()->getShopCurrency();
    }

    /**
     * Returns shop logout link
     *
     * @return string
     */
    public function getLogoutLink()
    {
        $sClass = $this->getTopActionClassName();
        $sCatnid = $this->getActCatId();
        $sMnfid = $this->getActManufacturerId();
        $sArtnid = $this->getActArticleId();
        $sTplName = $this->getActTplName();
        $sContentLoadId = $this->getActContentLoadId();
        $sSearchParam = $this->getActSearchParam();
        // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        $sRecommId = $this->getActRecommendationId();
        // END deprecated
        $sListType = $this->getActListType();

        $oConfig = Registry::getConfig();

        return ($oConfig->isSsl() ? $oConfig->getShopSecureHomeUrl() : $oConfig->getShopHomeUrl())
               . "cl={$sClass}"
               . ($sCatnid ? "&amp;cnid={$sCatnid}" : '')
               . ($sArtnid ? "&amp;anid={$sArtnid}" : '')
               . ($sMnfid ? "&amp;mnid={$sMnfid}" : '')
               . ($sSearchParam ? "&amp;searchparam={$sSearchParam}" : '')
               // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
               . ($sRecommId ? "&amp;recommid={$sRecommId}" : '')
               // END deprecated
               . ($sListType ? "&amp;listtype={$sListType}" : '')
               . "&amp;fnc=logout"
               . ($sTplName ? "&amp;tpl=" . basename($sTplName) : '')
               . ($sContentLoadId ? "&amp;oxloadid=" . $sContentLoadId : '')
               . "&amp;redirect=1";
    }

    /**
     * Returns help content link idents
     *
     * @return array
     */
    protected function getHelpContentIdents()
    {
        $sClass = $this->getActiveClassName();

        return ['oxhelp' . strtolower($sClass), 'oxhelpdefault'];
    }

    public function getMediaPictureUrl(): string
    {
        return Registry::getConfig()->getPictureUrl('media/');
    }

    /**
     * Returns shop help link
     *
     * @return string
     */
    public function getHelpPageLink()
    {
        if ($this->_sHelpPageLink === null) {
            $this->_sHelpPageLink = "";
            $aContentIdents = $this->getHelpContentIdents();
            $oContent = oxNew(\OxidEsales\Eshop\Application\Model\Content::class);
            foreach ($aContentIdents as $sIdent) {
                if ($oContent->loadByIdent($sIdent, true)) {
                    $this->_sHelpPageLink = $oContent->getLink();
                    break;
                }
            }
        }

        return $this->_sHelpPageLink;
    }

    /**
     * Returns active category id
     *
     * @return string
     */
    public function getActCatId()
    {
        return Registry::getRequest()->getRequestEscapedParameter('cnid');
    }

    /**
     * Returns active article id
     *
     * @return string
     */
    public function getActArticleId()
    {
        return Registry::getRequest()->getRequestEscapedParameter('anid');
    }

    /**
     * Returns active search parameter
     *
     * @return string
     */
    public function getActSearchParam()
    {
        return Registry::getRequest()->getRequestEscapedParameter('searchparam');
    }

    /**
     * Returns active recommendation id parameter
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return string
     */
    public function getActRecommendationId()
    {
        return Registry::getRequest()->getRequestEscapedParameter('recommid');
    }

    /**
     * Returns active listtype parameter
     *
     * @return string
     */
    public function getActListType()
    {
        return Registry::getRequest()->getRequestEscapedParameter('listtype');
    }

    /**
     * Returns active manufacturer id
     *
     * @return string
     */
    public function getActManufacturerId()
    {
        return Registry::getRequest()->getRequestEscapedParameter('mnid');
    }

    /**
     * Returns active content id
     *
     * @return string
     */
    public function getContentId()
    {
        return Registry::getRequest()->getRequestEscapedParameter('oxcid');
    }

    /**
     * Sets view config parameter, which can be accessed in templates in two ways:
     *
     * $oViewConf->getViewConfigParam( $sName )
     *
     * @param string $sName  name of parameter
     * @param mixed  $sValue parameter value
     */
    public function setViewConfigParam($sName, $sValue)
    {
        startProfile('\OxidEsales\Eshop\Core\ViewConfig::setViewConfigParam');

        $this->_aConfigParams[$sName] = $sValue;

        stopProfile('\OxidEsales\Eshop\Core\ViewConfig::setViewConfigParam');
    }

    /**
     * Returns current view config parameter
     *
     * @param string $sName name of parameter to get
     *
     * @return mixed
     */
    public function getViewConfigParam($sName)
    {
        startProfile('\OxidEsales\Eshop\Core\ViewConfig::getViewConfigParam');

        if ($this->_oShop && isset($this->_oShop->$sName)) {
            $sValue = $this->_oShop->$sName;
        } elseif ($this->_aViewData && isset($this->_aViewData[$sName])) {
            $sValue = $this->_aViewData[$sName];
        } else {
            $sValue = (isset($this->_aConfigParams[$sName]) ? $this->_aConfigParams[$sName] : null);
        }

        stopProfile('\OxidEsales\Eshop\Core\ViewConfig::getViewConfigParam');

        return $sValue;
    }

    /**
     * Sets shop object and view data to view config. This is needed mostly for
     * old templates
     *
     * @param \OxidEsales\Eshop\Application\Model\Shop $oShop     shop object
     * @param array                                    $aViewData view data array
     */
    public function setViewShop($oShop, $aViewData)
    {
        $this->_oShop = $oShop;
        $this->_aViewData = $aViewData;
    }

    /**
     * Returns forms hidden session parameters
     *
     * @return string
     */
    public function getHiddenSid()
    {
        if (($sValue = $this->getViewConfigParam('hiddensid')) === null) {
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $sValue = $session->hiddenSid();

            // appending language info to form
            if (($sLang = \OxidEsales\Eshop\Core\Registry::getLang()->getFormLang())) {
                $sValue .= "\n{$sLang}";
            }

            $sValue .= $this->getAdditionalRequestParameters();

            $this->setViewConfigParam('hiddensid', $sValue);
        }

        return $sValue;
    }

    /**
     * If any hidden parameters needed for sending with request
     *
     * @return string
     */
    protected function getAdditionalRequestParameters()
    {
        return '';
    }

    public function getShopUrl()
    {
        return Registry::getConfig()->getShopUrl();
    }

    /**
     * Returns shops self link
     *
     * @return string
     */
    public function getSelfLink()
    {
        if (($sValue = $this->getViewConfigParam('selflink')) === null) {
            $sValue = Registry::getConfig()->getShopHomeUrl();
            $this->setViewConfigParam('selflink', $sValue);
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
        if ($this->isAdmin()) {
            // using getSelfLink() method in admin mode (#2745)
            return $this->getSelfLink();
        }

        if (($sValue = $this->getViewConfigParam('sslselflink')) === null) {
            $sValue = Registry::getConfig()->getShopSecureHomeURL();
            $this->setViewConfigParam('sslselflink', $sValue);
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
        if (($sValue = $this->getViewConfigParam('basedir')) === null) {
            if (Registry::getConfig()->isSsl()) {
                $sValue = Registry::getConfig()->getSSLShopURL();
            } else {
                $sValue = Registry::getConfig()->getShopURL();
            }

            $this->setViewConfigParam('basedir', $sValue);
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
        if (($sValue = $this->getViewConfigParam('coreutilsdir')) === null) {
            $sValue = Registry::getConfig()->getCoreUtilsURL();
            $this->setViewConfigParam('coreutilsdir', $sValue);
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
        if (($sValue = $this->getViewConfigParam('selfactionlink')) === null) {
            $sValue = Registry::getConfig()->getShopCurrentUrl();
            $this->setViewConfigParam('selfactionlink', $sValue);
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
        if (($sValue = $this->getViewConfigParam('currenthomedir')) === null) {
            $sValue = Registry::getConfig()->getCurrentShopUrl();
            $this->setViewConfigParam('currenthomedir', $sValue);
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
        if (($sValue = $this->getViewConfigParam('basketlink')) === null) {
            $sValue = Registry::getConfig()->getShopHomeUrl() . 'cl=basket';
            $this->setViewConfigParam('basketlink', $sValue);
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
        if (($sValue = $this->getViewConfigParam('orderlink')) === null) {
            $sValue = Registry::getConfig()->getShopSecureHomeUrl() . 'cl=user';
            $this->setViewConfigParam('orderlink', $sValue);
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
        if (($sValue = $this->getViewConfigParam('paymentlink')) === null) {
            $sValue = Registry::getConfig()->getShopSecureHomeUrl() . 'cl=payment';
            $this->setViewConfigParam('paymentlink', $sValue);
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
        if (($sValue = $this->getViewConfigParam('exeorderlink')) === null) {
            $sValue = Registry::getConfig()->getShopSecureHomeUrl() . 'cl=order&amp;fnc=execute';
            $this->setViewConfigParam('exeorderlink', $sValue);
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
        if (($sValue = $this->getViewConfigParam('orderconfirmlink')) === null) {
            $sValue = Registry::getConfig()->getShopSecureHomeUrl() . 'cl=order';
            $this->setViewConfigParam('orderconfirmlink', $sValue);
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
    public function getResourceUrl($sFile = null)
    {
        if ($sFile) {
            $sValue = Registry::getConfig()->getResourceUrl($sFile, $this->isAdmin());
        } elseif (($sValue = $this->getViewConfigParam('basetpldir')) === null) {
            $sValue = Registry::getConfig()->getResourceUrl('', $this->isAdmin());
            $this->setViewConfigParam('basetpldir', $sValue);
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
        if (($sValue = $this->getViewConfigParam('templatedir')) === null) {
            $sValue = Registry::getConfig()->getTemplateDir($this->isAdmin());
            $this->setViewConfigParam('templatedir', $sValue);
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
        if (($sValue = $this->getViewConfigParam('urltemplatedir')) === null) {
            $sValue = Registry::getConfig()->getTemplateUrl($this->isAdmin());
            $this->setViewConfigParam('urltemplatedir', $sValue);
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
    public function getImageUrl($sFile = null, $bSsl = null)
    {
        if ($sFile) {
            $sValue = Registry::getConfig()->getImageUrl($this->isAdmin(), $bSsl, null, $sFile);
        } elseif (($sValue = $this->getViewConfigParam('imagedir')) === null) {
            $sValue = Registry::getConfig()->getImageUrl($this->isAdmin(), $bSsl);
            $this->setViewConfigParam('imagedir', $sValue);
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
        if (($sValue = $this->getViewConfigParam('nossl_imagedir')) === null) {
            $sValue = Registry::getConfig()->getImageUrl($this->isAdmin(), false);
            $this->setViewConfigParam('nossl_imagedir', $sValue);
        }

        return $sValue;
    }

    /**
     * Returns url to pictures directory.
     *
     * @return string
     */
    public function getPictureDir()
    {
        if (($sValue = $this->getViewConfigParam('picturedir')) === null) {
            $sValue = Registry::getConfig()->getPictureUrl(null, $this->isAdmin());
            $this->setViewConfigParam('picturedir', $sValue);
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
        if (($sValue = $this->getViewConfigParam('sAdminDir')) === null) {
            $sValue = Registry::getConfig()->getConfigParam('sAdminDir');
            $this->setViewConfigParam('sAdminDir', $sValue);
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
        if (($sValue = $this->getViewConfigParam('shopid')) === null) {
            $sValue = Registry::getConfig()->getShopId();
            $this->setViewConfigParam('shopid', $sValue);
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
        if (($sValue = $this->getViewConfigParam('isssl')) === null) {
            $sValue = Registry::getConfig()->isSsl();
            $this->setViewConfigParam('isssl', $sValue);
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
        if (($sValue = $this->getViewConfigParam('ip')) === null) {
            $sValue = \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getRemoteAddress();
            $this->setViewConfigParam('ip', $sValue);
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
        if (($sValue = $this->getViewConfigParam('popupident')) === null) {
            $sValue = md5(Registry::getConfig()->getShopUrl());
            $this->setViewConfigParam('popupident', $sValue);
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
        if (($sValue = $this->getViewConfigParam('popupidentrand')) === null) {
            $sValue = md5(time());
            $this->setViewConfigParam('popupidentrand', $sValue);
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
        if (($sValue = $this->getViewConfigParam('artperpageform')) === null) {
            $sValue = Registry::getConfig()->getShopCurrentUrl();
            $this->setViewConfigParam('artperpageform', $sValue);
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
        return Registry::getConfig()->getConfigParam('blVariantParentBuyable');
    }

    /**
     * Returns config param "blShowBirthdayFields" value
     *
     * @return string
     */
    public function showBirthdayFields()
    {
        return Registry::getConfig()->getConfigParam('blShowBirthdayFields');
    }

    /**
     * Returns config param "aNrofCatArticles" value
     *
     * @return array
     */
    public function getNrOfCatArticles()
    {
        $sListType = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('ldtype');

        if (is_null($sListType)) {
            $sListType = Registry::getConfig()->getConfigParam('sDefaultListDisplayType');
        }

        if ('grid' === $sListType) {
            $aNrOfCatArticles = Registry::getConfig()->getConfigParam('aNrofCatArticlesInGrid');
        } else {
            $aNrOfCatArticles = Registry::getConfig()->getConfigParam('aNrofCatArticles');
        }

        return $aNrOfCatArticles;
    }

    /**
     * Returns config param "bl_showWishlist" value
     *
     * @return bool
     */
    public function getShowWishlist()
    {
        return Registry::getConfig()->getConfigParam('bl_showWishlist');
    }

    /**
     * Returns config param "bl_showCompareList" value
     *
     * @return bool
     */
    public function getShowCompareList()
    {
        $myConfig = Registry::getConfig();
        $blShowCompareList = true;

        if (
            !$myConfig->getConfigParam('bl_showCompareList') ||
            ($myConfig->getConfigParam('blDisableNavBars') && $myConfig->getActiveView()->getIsOrderStep())
        ) {
            $blShowCompareList = false;
        }

        return $blShowCompareList;
    }

    /**
     * Returns config param "bl_showListmania" value
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return bool
     */
    public function getShowListmania()
    {
        return Registry::getConfig()->getConfigParam('bl_showListmania');
    }

    /**
     * Returns config param "bl_showVouchers" value
     *
     * @return bool
     */
    public function getShowVouchers()
    {
        return Registry::getConfig()->getConfigParam('bl_showVouchers');
    }

    /**
     * Returns config param "bl_showGiftWrapping" value
     *
     * @return bool
     */
    public function getShowGiftWrapping()
    {
        return Registry::getConfig()->getConfigParam('bl_showGiftWrapping');
    }

    /**
     * Returns session language id
     *
     * @return string
     */
    public function getActLanguageId()
    {
        $sValue = $this->getViewConfigParam('lang');
        if ($sValue === null) {
            $languageService = \OxidEsales\Eshop\Core\Registry::getLang();
            $request = \OxidEsales\Eshop\Core\Registry::getRequest();

            $iLang = $request->getRequestParameter('lang');
            $sValue = ($iLang !== null) ? $languageService->validateLanguage($iLang) : $languageService->getBaseLanguage();
            $this->setViewConfigParam('lang', $sValue);
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
        return \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageAbbr($this->getActLanguageId());
    }

    /**
     * Returns name of active view class
     *
     * @return string
     */
    public function getActiveClassName()
    {
        return Registry::getConfig()->getActiveView()->getClassKey();
    }

    /**
     * Returns name of a class of top view in the chain
     * (given a generic fnc, e.g. logout)
     *
     * @return string
     */
    public function getTopActiveClassName()
    {
        return Registry::getConfig()->getTopActiveView()->getClassKey();
    }

    /**
     * Returns max number of items shown on page
     *
     * @return int
     */
    public function getArtPerPageCount()
    {
        return $this->getViewConfigParam('iartPerPage');
    }

    /**
     * Returns navigation url parameters
     *
     * @return string
     */
    public function getNavUrlParams()
    {
        if (($sParams = $this->getViewConfigParam('navurlparams')) === null) {
            $sParams = '';
            $aNavParams = Registry::getConfig()->getActiveView()->getNavigationParams();
            foreach ($aNavParams as $sName => $sValue) {
                if (isset($sValue)) {
                    if ($sParams) {
                        $sParams .= '&amp;';
                    }
                    $sParams .= "{$sName}=" . rawurlencode($sValue);
                }
            }
            if ($sParams) {
                $sParams = '&amp;' . $sParams;
            }
            $this->setViewConfigParam('navurlparams', $sParams);
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
        if (($sParams = $this->getViewConfigParam('navformparams')) === null) {
            $oStr = Str::getStr();
            $sParams = '';
            $aNavParams = Registry::getConfig()->getTopActiveView()->getNavigationParams();
            foreach ($aNavParams as $sName => $sValue) {
                if (isset($sValue)) {
                    $sParams .= "<input type=\"hidden\" name=\"{$sName}\" value=\"";
                    $sParams .= $oStr->htmlentities($sValue) . "\" />\n";
                }
            }
            $this->setViewConfigParam('navformparams', $sParams);
        }

        return $sParams;
    }

    public function getStockOnDefaultMessage()
    {
        return Registry::getConfig()->getConfigParam('blStockOnDefaultMessage');
    }

    public function getStockOffDefaultMessage()
    {
        return Registry::getConfig()->getConfigParam('blStockOffDefaultMessage');
    }

    public function getStockLowDefaultMessage(): bool
    {
        return (bool) Registry::getConfig()->getConfigParam('blStockLowDefaultMessage');
    }

    /**
     * Returns shop version defined in view
     *
     * @return string
     */
    public function getShopVersion()
    {
        return $this->getViewConfigParam('sShopVersion');
    }

    /**
     * Returns AJAX request url
     *
     * @return  string
     */
    public function getAjaxLink()
    {
        return $this->getViewConfigParam('ajaxlink');
    }

    /**
     * Returns multishop status
     *
     * @return bool
     */
    public function isMultiShop()
    {
        $oShop = Registry::getConfig()->getActiveShop();

        return isset($oShop->oxshops__oxismultishop) ? ((bool) $oShop->oxshops__oxismultishop->value) : false;
    }

    /**
     * Returns session Remote Access token. Later you can pass the token over rtoken URL param
     * when you want to access the shop, for example, from different client.
     *
     * @return string
     */
    public function getRemoteAccessToken()
    {
        return \OxidEsales\Eshop\Core\Registry::getSession()->getRemoteAccessToken();
    }

    /**
     * Returns name of a view class, which will be active for an action
     * (given a generic fnc, e.g. logout)
     *
     * @return string
     */
    public function getActionClassName()
    {
        return Registry::getConfig()->getActiveView()->getActionClassName();
    }

    /**
     * Returns name of a class of top view in the chain
     * (given a generic fnc, e.g. logout)
     *
     * @return string
     */
    public function getTopActionClassName()
    {
        return Registry::getConfig()->getTopActiveView()->getActionClassName();
    }

    /**
     * should basket timeout counter be shown?
     *
     * @return bool
     */
    public function getShowBasketTimeout()
    {
        $session = Registry::getSession();
        return Registry::getConfig()->getConfigParam('blPsBasketReservationEnabled')
               && ($session->getBasketReservations()->getTimeLeft() > 0);
    }

    /**
     * return the seconds left until basket expiration
     *
     * @return int
     */
    public function getBasketTimeLeft()
    {
        if (!isset($this->_dBasketTimeLeft)) {
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $this->_dBasketTimeLeft = $session->getBasketReservations()->getTimeLeft();
        }

        return $this->_dBasketTimeLeft;
    }

    /**
     * true if blocks javascript code be enabled in templates
     *
     * @return bool
     */
    public function isTplBlocksDebugMode()
    {
        return (bool) Registry::getConfig()->getConfigParam('blDebugTemplateBlocks');
    }

    /**
     * min length of password
     *
     * @return int
     */
    public function getPasswordLength()
    {
        return \OxidEsales\Eshop\Core\Registry::getInputValidator()->getPasswordLength();
    }

    /**
     * Return country list
     *
     * @return \OxidEsales\Eshop\Application\Model\CountryList
     */
    public function getCountryList()
    {
        if ($this->_oCountryList === null) {
            // passing country list
            $this->_oCountryList = oxNew(\OxidEsales\Eshop\Application\Model\CountryList::class);
            $this->_oCountryList->loadActiveCountries();
        }

        return $this->_oCountryList;
    }


    /**
     * Return path to the requested module file
     *
     * @param string $moduleId
     * @param string $filePath
     *
     * @return string
     * @throws FileException
     *
     */
    public function getModulePath(string $moduleId, string $filePath = ''): string
    {
        if (!$filePath || ($filePath[0] != '/')) {
            $filePath = '/' . $filePath;
        }

        $filePath = $this
            ->getContainer()
            ->get(ModuleAssetsPathResolverBridgeInterface::class)
            ->getAssetsPath($moduleId) . $filePath;

        $this->validateModuleFile($filePath, $moduleId);

        return $filePath;
    }

    /**
     * return url to the requested module file
     *
     * @param string $sModule module name (directory name in modules dir)
     * @param string $sFile   file name to lookup
     *
     * @throws \oxFileException
     *
     * @return string
     */
    public function getModuleUrl($sModule, $sFile = '')
    {
        $c = Registry::getConfig();
        $shopUrl = null;
        if ($this->isAdmin()) {
            if ($c->isSsl()) {
                // From admin and with SSL we try to use sAdminSSLURL config directive
                $shopUrl = $c->getConfigParam('sAdminSSLURL');
                if ($shopUrl) {
                    // but we don't need the admin directory
                    $adminDir = '/' . $c->getConfigParam('sAdminDir');
                    $shopUrl = substr($shopUrl, 0, -strlen($adminDir));
                } else {
                    // if no sAdminSSLURL directive were defined we use sSSLShopURL config directive instead
                    $shopUrl = $c->getConfigParam('sSSLShopURL');
                }
            }
            // From admin and with no config usefull directive, we use the sShopURL directive
            if (!$shopUrl) {
                $shopUrl = $c->getConfigParam('sShopURL');
            }
        }
        // We are either in front, or in admin with no $sShopURL defined
        if (!$shopUrl) {
            $shopUrl = $c->getCurrentShopUrl();
        }
        $shopUrl = rtrim($shopUrl, '/');

        $sUrl = str_replace(
            rtrim($c->getConfigParam('sShopDir'), '/'),
            $shopUrl,
            $this->getModulePath($sModule, $sFile)
        );

        return $sUrl;
    }

    /**
     * return param value
     *
     * @param string $sName param name
     *
     * @return mixed
     */
    public function getViewThemeParam($sName)
    {
        $sValue = false;
        if (Registry::getConfig()->isThemeOption($sName)) {
            $sValue = Registry::getConfig()->getConfigParam($sName);
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
        return (bool) Registry::getConfig()->getConfigParam('bl_perfLoadSelectLists');
    }

    /**
     * Returns true if selection lists must be displayed in details page
     *
     * @return bool
     */
    public function showSelectListsInList()
    {
        return $this->showSelectLists() && (bool) Registry::getConfig()->getConfigParam('bl_perfLoadSelectListsInAList');
    }

    /**
     * Checks if alternative image server is configured.
     *
     * @return bool
     */
    public function isAltImageServerConfigured()
    {
        $oConfig = Registry::getConfig();

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
    public function isFunctionalityEnabled($sParamName)
    {
        return (bool) Registry::getConfig()->getConfigParam($sParamName);
    }

    /**
     * Returns active theme name
     *
     * @return string
     */
    public function getActiveTheme()
    {
        if ($this->_sActiveTheme === null) {
            $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
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
        if (is_null($this->_sShopLogo)) {
            $sLogoImage = Registry::getConfig()->getConfigParam('sShopLogo');
            if (empty($sLogoImage)) {
                $sLogoImage = "logo_" . strtolower((new Facts())->getEdition()) . ".png";
            }

            $this->setShopLogo($sLogoImage);
        }

        return $this->_sShopLogo;
    }

    /**
     * Sets shop logo
     *
     * @param string $sLogo shop logo image file name
     */
    public function setShopLogo($sLogo)
    {
        $this->_sShopLogo = $sLogo;
    }

    /**
     * retrieve session challenge token from session
     *
     * @return string
     */
    public function getSessionChallengeToken()
    {
        if (\OxidEsales\Eshop\Core\Registry::getSession()->isSessionStarted()) {
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $sessionChallengeToken = $session->getSessionChallengeToken();
        } else {
            $sessionChallengeToken = "";
        }

        return $sessionChallengeToken;
    }

    /**
     * Checks whether module is enabled.
     *
     * @param string $moduleId Module id
     *
     * @return bool
     */
    private function isModuleEnabled($moduleId): bool
    {
        $moduleActivationBridge = $this
            ->getContainer()
            ->get(ModuleActivationBridgeInterface::class);
        return $moduleActivationBridge->isActive(
            $moduleId,
            Registry::getConfig()->getShopId()
        );
    }

    /**
     * Return shop edition (EE|CE|PE)
     *
     * @return string
     */
    public function getEdition()
    {
        return (new Facts())->getEdition();
    }

    /**
     * Hook for modules.
     * Returns array of params => values which are used in hidden forms and as additional url params.
     * NOTICE: this method SHOULD return raw (non encoded into entities) parameters, because values
     * are processed by htmlentities() to avoid security and broken templates problems
     *
     * @return array
     */
    public function getAdditionalNavigationParameters()
    {
        return [];
    }

    /**
     * Hook for modules.
     * Template variable getter. Returns additional params for url
     *
     * @return string
     */
    public function getAdditionalParameters()
    {
        return '';
    }

    /**
     * Hook for modules.
     * Collects additional _GET parameters used by eShop
     *
     * @return string
     */
    public function addRequestParameters()
    {
        return '';
    }

    /**
     * Hook for modules.
     * returns additional url params for dynamic url building
     *
     * @param string $listType
     *
     * @return string
     */
    public function getDynUrlParameters($listType)
    {
        return '';
    }

    /**
     * @param string $filePath
     * @param string $moduleId
     * @throws FileException
     */
    private function validateModuleFile(string $filePath, string $moduleId): void
    {
        if (!file_exists($filePath)) {
            $exception = new FileException("Requested file not found for module $moduleId ($filePath)");
            if (Registry::getConfig()->getConfigParam('iDebug')) {
                throw $exception;
            } else {
                Registry::getLogger()->error($exception->getMessage(), [$exception]);
            }
        }
    }
}
