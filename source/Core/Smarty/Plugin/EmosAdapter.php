<?php

/**
 * Copyright (c) 2004 - 2007 ECONDA GmbH Karlsruhe
 * All rights reserved.
 *
 * ECONDA GmbH
 * Haid-und-Neu-Str. 7
 * 76131 Karlsruhe
 * Tel. +49 (721) 6630350
 * Fax +49 (721) 66303510
 * info@econda.de
 * www.econda.de
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 * Neither the name of the ECONDA GmbH nor the names of its contributors may
 * be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *  $Id$
 */

namespace OxidEsales\EshopCommunity\Core\Smarty\Plugin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;

/**
 * This class is a reference implementation of a PHP Function to include
 * ECONDA Trackiong into a Shop-System.
 *
 * @deprecated since v6.5.6 (2020-07-29); moved the functionality to 'OXID personalization' module
 *
 * The smarty tempaltes should include s tag like
 * [{insert name="oxid_tracker" title=$template_title}]
 */
class EmosAdapter extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Current view category path
     *
     * @var string
     */
    protected $_sEmosCatPath = null;

    /**
     * Emos object storage
     *
     * @var \OxidEsales\Eshop\Core\Smarty\Plugin\Emos
     */
    protected $_oEmos = null;

    /**
     * Emos pages content
     *
     * @var array
     */
    protected $_aPagesContent = [
        'start' => 'Start',
        'basket' => 'Shop/Kaufprozess/Warenkorb',
        'user' => 'Shop/Kaufprozess/Kundendaten',
        'user_1' => 'Shop/Kaufprozess/Kundendaten/OhneReg',
        'user_2' => 'Shop/Kaufprozess/Kundendaten/BereitsKunde',
        'user_3' => 'Shop/Kaufprozess/Kundendaten/NeuesKonto',
        'payment' => 'Shop/Kaufprozess/Zahlungsoptionen',
        'order' => 'Shop/Kaufprozess/Bestelluebersicht',
        'thankyou' => 'Shop/Kaufprozess/Bestaetigung',
        'search' => 'Shop/Suche',
        'account_wishlist' => 'Service/Wunschzettel',
        'contact_success' => 'Service/Kontakt/Success',
        'contact_failure' => 'Service/Kontakt/Form',
        'help' => 'Service/Hilfe',
        'newsletter_success' => 'Service/Newsletter/Success',
        'newsletter_failure' => 'Service/Newsletter/Form',
        'links' => 'Service/Links',
        'info_impressum.tpl' => 'Info/Impressum',
        'info_agb.tpl' => 'Info/AGB',
        'info_order_info.tpl' => 'Info/Bestellinfo',
        'info_delivery_info.tpl' => 'Info/Versandinfo',
        'info_security_info.tpl' => 'Info/Sicherheit',
        'account_login' => 'Login/Uebersicht',
        'account_logout' => 'Login/Formular/Logout',
        'account_needlogin' => 'Login/Formular/Login',
        'account_user' => 'Login/Kundendaten',
        'account_order' => 'Login/Bestellungen',
        'account_noticelist' => 'Login/Merkzettel',
        'account_newsletter' => 'Login/Newsletter',
        'account_whishlist' => 'Login/Wunschzettel',
        'forgotpassword' => 'Login/PW vergessen',
        'content_oximpressum' => 'Info/Impressum',
        'content_oxagb' => 'Info/AGB',
        'content_oxorderinfo' => 'Info/Bestellinfo',
        'content_oxdeliveryinfo' => 'Info/Versandinfo',
        'content_oxsecurityinfo' => 'Info/Sicherheit',
        'register' => 'Service/Register',
    ];

    /**
     * Emos order step names
     *
     * @var array
     */
    protected $_aOrderStepNames = [
        'basket' => '1_Warenkorb',
        'order_process' => '2_Kundendaten',
        'user' => '2_Kundendaten',
        'user_1' => '2_Kundendaten/OhneReg',
        'user_2' => '2_Kundendaten/BereitsKunde',
        'user_3' => '2_Kundendaten/NeuesKonto',
        'payment' => '3_Zahlungsoptionen',
        'order' => '4_Bestelluebersicht',
        'thankyou' => '5_Bestaetigung',
    ];

    /**
     * Returns new emos controller object
     *
     * @return \OxidEsales\Eshop\Core\Smarty\Plugin\Emos
     */
    public function getEmos()
    {
        if ($this->_oEmos === null) {
            $this->_oEmos = new Emos($this->getScriptPath());

            // make output more readable
            $this->_oEmos->prettyPrint();
            //$this->_oEmos->setSid( $this->getSession()->getId() );

            // set page id
            $this->_oEmos->addPageId($this->getEmosPageId($this->getTplName()));

            // language id
            $this->_oEmos->addLangId(\OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage());

            // set site ID
            $this->_oEmos->addSiteId(\OxidEsales\Eshop\Core\Registry::getConfig()->getShopId());
        }

        return $this->_oEmos;
    }

    /**
     * Builds JS code for current view tracking functionality
     *
     * @param array $aParams plugin parameters
     * @param \Smarty $oSmarty template engine object
     *
     * @return string
     */
    public function getCode($aParams, $oSmarty)
    {
        $oEmos = $this->getEmos();

        $this->setControllerInfo($oEmos, $aParams, $oSmarty);

        $this->setBasketActionsInfo($oEmos);

        return "\n" . $oEmos->toString();
    }

    /**
     * Returns path to econda script files
     *
     * @return string
     */
    protected function getScriptPath()
    {
        $sShopUrl = \OxidEsales\Eshop\Core\Registry::getConfig()->getCurrentShopUrl();

        return "{$sShopUrl}modules/econda/out/";
    }

    /**
     * Returns emos item object
     *
     * @return \OxidEsales\Eshop\Core\Smarty\Plugin\EmosItem
     */
    protected function getNewEmosItem()
    {
        return new \OxidEsales\Eshop\Core\Smarty\Plugin\EmosItem();
    }

    /**
     * Returns formatted product title
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oProduct product which title must be prepared
     *
     * @return string
     */
    protected function prepareProductTitle($oProduct)
    {
        $sTitle = $oProduct->oxarticles__oxtitle->value;
        if ($oProduct->oxarticles__oxvarselect->value) {
            $sTitle .= " " . $oProduct->oxarticles__oxvarselect->value;
        }

        return $sTitle;
    }

    /**
     * Converts a oxarticle object to an EMOS_Item
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oProduct article to convert
     * @param string $sCatPath category path
     * @param int $iQty buyable amount
     *
     * @return \OxidEsales\Eshop\Core\Smarty\Plugin\EmosItem
     */
    protected function convProd2EmosItem($oProduct, $sCatPath = "NULL", $iQty = 1)
    {
        $oItem = $this->getNewEmosItem();

        $sProductId = (isset($oProduct->oxarticles__oxartnum->value) && $oProduct->oxarticles__oxartnum->value) ? $oProduct->oxarticles__oxartnum->value : $oProduct->getId();
        $oItem->productId = $sProductId;
        $oItem->productName = $this->prepareProductTitle($oProduct);

        // #810A
        $oCur = \OxidEsales\Eshop\Core\Registry::getConfig()->getActShopCurrencyObject();
        $oItem->price = $oProduct->getPrice()->getBruttoPrice() * (1 / $oCur->rate);
        $oItem->productGroup = "{$sCatPath}/{$oProduct->oxarticles__oxtitle->value}";
        $oItem->quantity = $iQty;
        // #3452: Add brands to econda tracking
        $oItem->variant1 = $oProduct->getVendor() ? $oProduct->getVendor()->getTitle() : "NULL";
        $oItem->variant2 = $oProduct->getManufacturer() ? $oProduct->getManufacturer()->getTitle() : "NULL";
        $oItem->variant3 = $oProduct->getId();

        return $oItem;
    }

    /**
     * Returns page title
     *
     * @param array $aParams parameters where product info is kept
     *
     * @return string
     */
    protected function getEmosPageTitle($aParams)
    {
        return isset($aParams['title']) ? $aParams['title'] : null;
    }

    /**
     * Returns purpose of this page (current view name)
     *
     * @return string
     */
    protected function getEmosCl()
    {
        $oActView = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView();
        // showLogin function is deprecated, but just in case if it is called
        if (strcasecmp('showLogin', (string)$oActView->getFncName()) == 0) {
            $sCl = 'account';
        } else {
            $sCl = $oActView->getClassKey();
        }

        return $sCl ? strtolower($sCl) : 'start';
    }

    /**
     * Returns current view category path
     *
     * @return string
     */
    protected function getEmosCatPath()
    {
        // #4016: econda: json function returns null if title has an umlaut
        if ($this->_sEmosCatPath === null) {
            $aCatTitle = [];
            if ($aCatPath = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView()->getBreadCrumb()) {
                foreach ($aCatPath as $aCatPathParts) {
                    $aCatTitle[] = $aCatPathParts['title'];
                }
            }
            $this->_sEmosCatPath = (count($aCatTitle) ? strip_tags(implode('/', $aCatTitle)) : 'NULL');
        }

        return $this->_sEmosCatPath;
    }

    /**
     * Builds basket product category path
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article to build category id
     *
     * @return string
     */
    protected function getBasketProductCatPath($oArticle)
    {
        $sCatPath = '';
        if ($oCategory = $oArticle->getCategory()) {
            $sTable = $oCategory->getViewName();
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
            $sQ = "select {$sTable}.oxtitle as oxtitle from {$sTable}
                       where {$sTable}.oxleft <= :oxleft and
                             {$sTable}.oxright >= :oxright and
                             {$sTable}.oxrootid = :oxrootid
                       order by {$sTable}.oxleft";

            $oRs = $oDb->select($sQ, [
                ':oxleft' => $oCategory->oxcategories__oxleft->value,
                ':oxright' => $oCategory->oxcategories__oxright->value,
                ':oxrootid' => $oCategory->oxcategories__oxrootid->value,
            ]);
            if ($oRs != false && $oRs->count() > 0) {
                while (!$oRs->EOF) {
                    if ($sCatPath) {
                        $sCatPath .= '/';
                    }
                    $sCatPath .= strip_tags($oRs->fields['oxtitle']);
                    $oRs->fetchRow();
                }
            }
        }

        return $sCatPath;
    }

    /**
     * generates a unique id for the current page
     *
     * @param string $sTplName current view template name
     *
     * @return string
     */
    protected function getEmosPageId($sTplName)
    {
        $sPageId = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId() .
                   $this->getEmosCl() .
                   $sTplName .
                   Registry::getRequest()->getRequestEscapedParameter('cnid') .
                   Registry::getRequest()->getRequestEscapedParameter('anid') .
                   Registry::getRequest()->getRequestEscapedParameter('option');

        return md5($sPageId);
    }

    /**
     * Returns active view template name
     *
     * @return string
     */
    protected function getTplName()
    {
        if (!($sCurrTpl = basename((string)Registry::getRequest()->getRequestEscapedParameter('tpl')))) {
            // in case template was not defined in request
            $sCurrTpl = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView()->getTemplateName();
        }

        return $sCurrTpl;
    }

    /**
     * Returns page content array.
     *
     * @return array
     */
    private function getPagesContent()
    {
        return $this->_aPagesContent;
    }

    /**
     * Returns each order step name in array.
     *
     * @return array
     */
    private function getOrderStepNames()
    {
        return $this->_aOrderStepNames;
    }

    /**
     * Sets controller information in Emos.
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\Emos $oEmos
     * @param array $aParams
     * @param \Smarty $oSmarty
     */
    private function setControllerInfo($oEmos, $aParams, $oSmarty)
    {
        $sControllerName = $this->getEmosCl();
        $aContent = $this->getPagesContent();
        $aOrderSteps = $this->getOrderStepNames();

        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oCurrentView = $oConfig->getActiveView();
        $sFunction = $oCurrentView->getFncName();
        $oStr = Str::getStr();
        $sTplName = $this->getTplName();
        /** @var \OxidEsales\Eshop\Application\Model\User $oUser */
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        if (!$oUser->loadActiveUser()) {
            $oUser = false;
        }
        $oProduct = (isset($aParams['product']) && $aParams['product']) ? $aParams['product'] : null;

        switch ($sControllerName) {
            case 'user':
                $session = \OxidEsales\Eshop\Core\Registry::getSession();

                $sOption = Registry::getRequest()->getRequestEscapedParameter('option');
                $sOption = (isset($sOption)) ? $sOption : $session->getVariable('option');

                if (isset($sOption) && array_key_exists('user_' . $sOption, $aContent)) {
                    $aContent['user'] = $aContent['user_' . $sOption];
                }

                if (isset($sOption) && array_key_exists('user_' . $sOption, $aOrderSteps)) {
                    $aOrderSteps['user'] = $aOrderSteps['user_' . $sOption];
                }
                break;
            case 'payment':
                if (Registry::getRequest()->getRequestEscapedParameter('new_user')) {
                    $this->setUserRegistration($oEmos, $oUser);
                }
                break;
            case 'thankyou':
                /** @var \OxidEsales\Eshop\Application\Controller\ThankYouController $oCurrentView */
                $this->setBasketInformation($oEmos, $oUser, $oCurrentView->getOrder(), $oCurrentView->getBasket());
                break;
            case 'oxwarticledetails':
                if ($oProduct) {
                    $sPath = $this->getBasketProductCatPath($oProduct);
                    $sTitle = $this->prepareProductTitle($oProduct);
                    $aContent['oxwarticledetails'] = "Shop/{$sPath}/" . strip_tags($sTitle);
                    $oEmos->addDetailView($this->convProd2EmosItem($oProduct, $sPath, 1));
                }
                break;
            case 'search':
                $this->setSearchInformation($oEmos, $oSmarty);
                break;
            case 'alist':
                $aContent['alist'] = 'Shop/' . $this->getEmosCatPath();
                break;
            case 'account':
                if ($sFunction) {
                    $aContent['account'] = ($sFunction != 'logout') ? $aContent['account_login'] : $aContent['account_logout'];
                } else {
                    $aContent['account'] = $aContent['account_needlogin'];
                }
                break;
            case 'contact':
                /** @var \OxidEsales\Eshop\Application\Controller\ContactController $oCurrentView */
                if ($oCurrentView->getContactSendStatus()) {
                    $aContent['contact'] = $aContent['contact_success'];
                    $oEmos->addContact('Kontakt');
                } else {
                    $aContent['contact'] = $aContent['contact_failure'];
                }
                break;
            case 'newsletter':
                /** @var \OxidEsales\Eshop\Application\Controller\NewsletterController $oCurrentView */
                $aContent['newsletter'] = $oCurrentView->getNewsletterStatus() ? $aContent['newsletter_success'] : $aContent['newsletter_failure'];
                break;
            case 'info':
                if (array_key_exists('info_' . $sTplName, $aContent)) {
                    $aContent['info'] = $aContent['info_' . $sTplName];
                } else {
                    $aContent['info'] = 'Content/' . $oStr->preg_replace('/\.tpl$/', '', $sTplName);
                }
                break;
            case 'content':
                // backwards compatibility
                $oContent = ($oCurrentView instanceof \OxidEsales\Eshop\Application\Controller\ContentController) ? $oCurrentView->getContent() : null;
                $sContentId = $oContent ? $oContent->oxcontents__oxloadid->value : null;

                if (array_key_exists('content_' . $sContentId, $aContent)) {
                    $aContent['content'] = $aContent['content_' . $sContentId];
                } else {
                    $aContent['content'] = 'Content/' . $this->getEmosPageTitle($aParams);
                }
                break;
            case 'register':
                $this->setUserRegistration($oEmos, $oUser);
                break;
        }

        if (is_string($sControllerName) && array_key_exists($sControllerName, $aContent)) {
            $oEmos->addContent($aContent[$sControllerName]);
        } else {
            $oEmos->addContent('Content/' . $oStr->preg_replace('/\.tpl$/', '', $sTplName));
        }

        if (is_string($sControllerName) && array_key_exists($sControllerName, $aOrderSteps)) {
            $oEmos->addOrderProcess($aOrderSteps[$sControllerName]);
        }

        // track logins
        if ('login_noredirect' == $sFunction) {
            $oEmos->addLogin(Registry::getRequest()->getRequestEscapedParameter('lgn_usr'), $oUser ? '0' : '1');
        }
    }

    /**
     * Sets search page information to Emos.
     * Only tracking first search page, not the following pages.
     * #4018: The emospro.search string is URL-encoded forwarded to econda instead of URL-escaped.
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\Emos $oEmos
     * @param \Smarty $oSmarty
     */
    private function setSearchInformation($oEmos, $oSmarty)
    {
        $iPage = Registry::getRequest()->getRequestEscapedParameter('pgNr');
        if (!$iPage) {
            $sSearchParamForLink = Registry::getRequest()->getRequestEscapedParameter('searchparam');
            $iSearchCount = 0;
            if (($oSmarty->getTemplateVars()['oView']) && $oSmarty->getTemplateVars()['oView']->getArticleCount()) {
                $iSearchCount = $oSmarty->getTemplateVars()['oView']->getArticleCount();
            }
            $oEmos->addSearch($sSearchParamForLink, $iSearchCount);
        }
    }

    /**
     * Sets basket information to Emos.
     * Uses username (email address) instead of customer number.
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\Emos $oEmos
     * @param \OxidEsales\Eshop\Application\Model\User $oUser
     * @param \OxidEsales\Eshop\Application\Model\Order $oOrder
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket
     */
    private function setBasketInformation($oEmos, $oUser, $oOrder, $oBasket)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oCur = $oConfig->getActShopCurrencyObject();

        $oEmos->addEmosBillingPageArray(
            $oOrder->oxorder__oxordernr->value,
            $oUser->oxuser__oxusername->value,
            $oBasket->getPrice()->getBruttoPrice() * (1 / $oCur->rate),
            $oOrder->oxorder__oxbillcountry->value,
            $oOrder->oxorder__oxbillzip->value,
            $oOrder->oxorder__oxbillcity->value
        );

        // get Basket Page Array
        $aBasket = [];
        $aBasketProducts = $oBasket->getContents();
        foreach ($aBasketProducts as $oContent) {
            /** @var \OxidEsales\Eshop\Application\Model\BasketItem $oContent */
            $sId = $oContent->getProductId();

            /** @var \OxidEsales\Eshop\Application\Model\Article $oProduct */
            $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oProduct->load($sId);

            $sPath = $this->getBasketProductCatPath($oProduct);
            $aBasket[] = $this->convProd2EmosItem($oProduct, $sPath, $oContent->getAmount());
        }

        $oEmos->addEmosBasketPageArray($aBasket);
    }

    /**
     * Sets user registration action to Emos.
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\Emos $oEmos
     * @param \OxidEsales\Eshop\Application\Model\User $oUser
     */
    private function setUserRegistration($oEmos, $oUser)
    {
        $iError = Registry::getRequest()->getRequestEscapedParameter('newslettererror');
        $iSuccess = Registry::getRequest()->getRequestEscapedParameter('success');

        if ($iError && $iError < 0) {
            $oEmos->addRegister($oUser ? $oUser->getId() : 'NULL', abs($iError));
        }

        if ($iSuccess && $iSuccess > 0 && $oUser) {
            $oEmos->addRegister($oUser->getId(), 0);
        }
    }

    /**
     * Sets basket actions (update and add) information to Emos.
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\Emos $oEmos
     */
    private function setBasketActionsInfo($oEmos)
    {
        // get the last Call for special handling function "tobasket", "changebasket"
        if (($aLastCall = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('aLastcall'))) {
            \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable('aLastcall');
        }

        // ADD To Basket and Remove from Basket
        if (is_array($aLastCall) && count($aLastCall)) {
            $sCallAction = key($aLastCall);
            $aCallData = current($aLastCall);

            switch ($sCallAction) {
                case 'changebasket':
                    foreach ($aCallData as $sItemId => $aItemData) {
                        $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                        if ($aItemData['oldam'] > $aItemData['am'] && $oProduct->load($aItemData['aid'])) {
                            $sPath = $this->getBasketProductCatPath($oProduct);
                            $oEmos->removeFromBasket($this->convProd2EmosItem($oProduct, $sPath, ($aItemData['oldam'] - $aItemData['am'])));
                            //$oEmos->appendPreScript($aItemData['oldam'].'->'.$aItemData['am'].':'.$oProduct->load( $aItemData['aid']));
                        } elseif ($aItemData['oldam'] < $aItemData['am'] && $oProduct->load($aItemData['aid'])) {
                            $sPath = $this->getBasketProductCatPath($oProduct);
                            $oEmos->addToBasket($this->convProd2EmosItem($oProduct, $sPath, $aItemData['am'] - $aItemData['oldam']));
                        }
                    }
                    break;
                case 'tobasket':
                    foreach ($aCallData as $sItemId => $aItemData) {
                        // ECONDA FIX if there is a "add to basket" in the artcle list view, we do not have a product ID here
                        $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                        if ($oProduct->load($sItemId)) {
                            $sPath = $this->getBasketProductCatPath($oProduct);
                            $oEmos->addToBasket($this->convProd2EmosItem($oProduct, $sPath, $aItemData['am']));
                        }
                    }
                    break;
            }
        }
    }
}
