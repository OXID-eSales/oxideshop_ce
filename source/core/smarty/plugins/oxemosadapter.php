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


/**
 * Includes emos script formatter class
 */
require_once oxRegistry::getConfig()->getConfigParam( 'sCoreDir' ) . 'smarty/plugins/emos.php';

/**
 * This class is a reference implementation of a PHP Function to include
 * ECONDA Trackiong into a Shop-System.
 *
 * The smarty tempaltes should include s tag like
 * [{insert name="oxid_tracker" title=$template_title }]
 */
class oxEmosAdapter extends oxSuperCfg
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
     * @var emos
     */
    protected $_oEmos = null;

    /**
     * oxEmosAdapter class instance.
     *
     * @var oxEmosAdapter instance
     */
    private static $_instance = null;

    /**
     * Return a single instance of this class
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxEmosAdapter") instead.
     *
     * @return oxUtils
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxEmosAdapter");
    }

    /**
     * Returns path to econda script files
     *
     * @return string
     */
    protected function _getScriptPath()
    {
        $sShopUrl = $this->getConfig()->getCurrentShopUrl();
        return "{$sShopUrl}modules/econda/out/";
    }

    /**
     * Returns emos item object
     *
     * @return EMOS_Item
     */
    protected function _getNewEmosItem()
    {
        return new EMOS_Item();
    }

    /**
     * Returns new emos controller object
     *
     * @return emos
     */
    public function getEmos()
    {
        if ( $this->_oEmos === null ) {
            $this->_oEmos = new EMOS( $this->_getScriptPath() );

            // make output more readable
            $this->_oEmos->prettyPrint();
            //$this->_oEmos->setSid( $this->getSession()->getId() );

            // set page id
            $this->_oEmos->addPageId( $this->_getEmosPageId( $this->_getTplName() ) );

            // language id
            $this->_oEmos->addLangId( oxRegistry::getLang()->getBaseLanguage() );

            // set site ID
            $this->_oEmos->addSiteId( $this->getConfig()->getShopId() );
        }

        return $this->_oEmos;
    }

    /**
     * Checks whether shop is in utf, if not - iconv string for using with econda json_encode
     * 
     * @param string $sContent
     * 
     * @return string 
     */
    protected function _convertToUtf( $sContent ) 
    {
        $myConfig  = $this->getConfig();
        if ( !$myConfig->isUtf() ) {
            $sContent = iconv( oxLang::getInstance()->translateString( 'charset' ), 'UTF-8' , $sContent );
        }
        return $sContent;
    }


    /**
     * Returns formatted product title
     *
     * @param oxarticle $oProduct product which title must be prepared
     *
     * @return string
     */
    protected function _prepareProductTitle( $oProduct )
    {
        $sTitle = $oProduct->oxarticles__oxtitle->value;
        if ( $oProduct->oxarticles__oxvarselect->value ) {
            $sTitle .= " ".$oProduct->oxarticles__oxvarselect->value;
        }
        $sTitle = $this->_convertToUtf( $sTitle );
        return $sTitle;
    }

    /**
     * Converts a oxarticle object to an EMOS_Item
     *
     * @param oxarticle $oProduct article to convert
     * @param string    $sCatPath category path
     * @param int       $iQty     buyable amount
     *
     * @return EMOS_Item
     */
    protected function _convProd2EmosItem( $oProduct, $sCatPath = "NULL", $iQty = 1 )
    {
        $oItem = $this->_getNewEmosItem();

        $sProductId = ( isset( $oProduct->oxarticles__oxartnum->value ) && $oProduct->oxarticles__oxartnum->value ) ? $oProduct->oxarticles__oxartnum->value : $oProduct->getId();
        $oItem->productId   = $this->_convertToUtf( $sProductId );
        $oItem->productName = $this->_prepareProductTitle( $oProduct );

        // #810A
        $oCur = $this->getConfig()->getActShopCurrencyObject();
        $oItem->price        = $oProduct->getPrice()->getBruttoPrice() * ( 1/$oCur->rate );
        $oItem->productGroup = "{$sCatPath}/{$this->_convertToUtf( $oProduct->oxarticles__oxtitle->value )}";
        $oItem->quantity     = $iQty;
        // #3452: Add brands to econda tracking
        $oItem->variant1     = $oProduct->getVendor() ? $this->_convertToUtf( $oProduct->getVendor()->getTitle() ) : "NULL";
        $oItem->variant2     = $oProduct->getManufacturer() ? $this->_convertToUtf( $oProduct->getManufacturer()->getTitle() ) : "NULL";
        $oItem->variant3     = $oProduct->getId();

        return $oItem;
    }

    /**
     * Returns page title
     *
     * @param array $aParams parameters where product info is kept
     *
     * @return string
     */
    protected function _getEmosPageTitle( $aParams )
    {
        return isset( $aParams['title'] ) ? $this->_convertToUtf( $aParams['title'] ) : null;
    }

    /**
     * Returns purpose of this page (current view name)
     *
     * @return string
     */
    protected function _getEmosCl()
    {
        $oActView = $this->getConfig()->getActiveView();
        // showLogin function is deprecated, but just in case if it is called
        if ( strcasecmp( 'showLogin', (string) $oActView->getFncName() ) == 0 ) {
            $sCl = 'account';
        } else {
            $sCl = $oActView->getClassName();
        }
        return $sCl ? strtolower( $sCl ) : 'start';
    }

    /**
     * Returns current view category path
     *
     * @return string
     */
    protected function _getEmosCatPath() 
    {
        // #4016: econda: json function returns null if title has an umlaut
        if ($this->_sEmosCatPath === null) {
            $aCatTitle = array();
                if ( $aCatPath = $this->getConfig()->getActiveView()->getBreadCrumb() ) {
                    foreach ($aCatPath as $aCatPathParts) {
                        $aCatTitle[] = $aCatPathParts['title'];
                    }
                }
            $this->_sEmosCatPath = ( count($aCatTitle) ? strip_tags(implode('/', $aCatTitle)) : 'NULL' );
            $this->_sEmosCatPath = $this->_convertToUtf( $this->_sEmosCatPath );
        }
        return $this->_sEmosCatPath;
    }

    /**
     * Builds basket product category path
     *
     * @param oxarticle $oArticle article to build category id
     *
     * @return string
     */
    protected function _getBasketProductCatPath( $oArticle )
    {
        $sCatPath = '';
        if ( $oCategory = $oArticle->getCategory() ) {
            $sTable = $oCategory->getViewName();
            $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
            $sQ = "select {$sTable}.oxtitle as oxtitle from {$sTable}
                       where {$sTable}.oxleft <= ".$oDb->quote( $oCategory->oxcategories__oxleft->value )." and
                             {$sTable}.oxright >= ".$oDb->quote( $oCategory->oxcategories__oxright->value )." and
                             {$sTable}.oxrootid = ".$oDb->quote( $oCategory->oxcategories__oxrootid->value )."
                       order by {$sTable}.oxleft";

            $oRs = $oDb->execute( $sQ );
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while ( !$oRs->EOF ) {
                    if ( $sCatPath ) {
                        $sCatPath .= '/';
                    }
                    $sCatPath .= strip_tags( $oRs->fields['oxtitle'] );
                    $oRs->moveNext();
                }
            }
        }
        $sCatPath = $this->_convertToUtf( $sCatPath );
        
        return $sCatPath;
    }

    /**
     * generates a unique id for the current page
     *
     * @param string $sTplName current view template name
     *
     * @return string
     */
    protected function _getEmosPageId( $sTplName )
    {
        $sPageId = $this->getConfig()->getShopId() .
                   $this->_getEmosCl() .
                   $sTplName .
                   oxConfig::getParameter( 'cnid' ) .
                   oxConfig::getParameter( 'anid' ) .
                   oxConfig::getParameter( 'option' );

        return md5( $sPageId );
    }

    /**
     * Returns active view template name
     *
     * @return string
     */
    protected function _getTplName()
    {
        if ( !( $sCurrTpl = basename( ( string ) oxConfig::getParameter( 'tpl' ) ) ) ) {
            // in case template was not defined in request
            $sCurrTpl = $this->getConfig()->getActiveView()->getTemplateName();
        }
        return $sCurrTpl;
    }

    /**
     * Builds JS code for current view tracking functionality
     *
     * @param array  $aParams plugin parameters
     * @param smarty $oSmarty template engine object
     *
     * @return string
     */
    public function getCode( $aParams, $oSmarty )
    {
        $myConfig  = $this->getConfig();
        $mySession = $this->getSession();

        // current view object
        $oCurrView = $myConfig->getActiveView();

        // action name
        $sFnc = $oCurrView->getFncName();

        // current product (if available)
        $oProduct = ( isset( $aParams['product'] ) && $aParams['product'] ) ? $aParams['product'] : null;

        // session user
        $oUser = oxNew( 'oxuser' );
        if ( !$oUser->loadActiveUser() ) {
            $oUser = false;
        }

        // make a new emos instance for this call
        $oEmos = $this->getEmos();

        // current name of tmeplate
        $sTplName = $this->_getTplName();

        // current currency
        $oCur = $myConfig->getActShopCurrencyObject();
        $oStr = getStr();

        // treat the different PageTypes
        switch ( $this->_getEmosCl() ) {
            case 'start':
                $oEmos->addContent( 'Start' );
                break;
            case 'basket':
                $oEmos->addContent( 'Shop/Kaufprozess/Warenkorb' );
                $oEmos->addOrderProcess( '1_Warenkorb' );
                break;
            case 'user':
                //ECONDA FIX track the OXID 3.x order process with the 3 different options plus default
                $sOption = oxConfig::getParameter( 'option' );
                $sOption = ( isset( $sOption ) ) ? $sOption : oxSession::getVar( 'option' );
                switch ( $sOption ) {
                    case '1':
                        $oEmos->addContent( 'Shop/Kaufprozess/Kundendaten/OhneReg' );
                        $oEmos->addOrderProcess( '2_Kundendaten/OhneReg' );
                        break;
                    case '2':
                        $oEmos->addContent( 'Shop/Kaufprozess/Kundendaten/BereitsKunde' );
                        $oEmos->addOrderProcess( '2_Kundendaten/BereitsKunde' );
                        break;
                    case '3':
                        $oEmos->addContent( 'Shop/Kaufprozess/Kundendaten/NeuesKonto' );
                        $oEmos->addOrderProcess( '2_Kundendaten/NeuesKonto' );
                        break;
                    default:
                        $oEmos->addContent( 'Shop/Kaufprozess/Kundendaten' );
                        $oEmos->addOrderProcess( '2_Kundendaten' );
                        break;
                }
                break;
            case 'payment':
                $oEmos->addContent( 'Shop/Kaufprozess/Zahlungsoptionen' );
                $oEmos->addOrderProcess( '3_Zahlungsoptionen' );
                break;
            case 'order':
                $oEmos->addContent( 'Shop/Kaufprozess/Bestelluebersicht' );
                $oEmos->addOrderProcess( '4_Bestelluebersicht' );
                break;
            case 'thankyou':
                $oEmos->addContent( 'Shop/Kaufprozess/Bestaetigung' );
                $oEmos->addOrderProcess( '5_Bestaetigung' );

                // get order Page Array
                //ECONDA FIX use username (email address) instead of customer number
                $oOrder  = $oCurrView->getOrder();
                $oBasket = $oCurrView->getBasket();
                $oEmos->addEmosBillingPageArray( $this->_convertToUtf($oOrder->oxorder__oxordernr->value),
                                                 $this->_convertToUtf($oUser->oxuser__oxusername->value),
                                                 $oBasket->getPrice()->getBruttoPrice() * ( 1 / $oCur->rate ),
                                                 $this->_convertToUtf($oOrder->oxorder__oxbillcountry->value),
                                                 $this->_convertToUtf($oOrder->oxorder__oxbillzip->value),
                                                 $this->_convertToUtf($oOrder->oxorder__oxbillcity->value) );

                // get Basket Page Array
                $aBasket = array();
                $aBasketProducts = $oBasket->getContents();
                foreach ( $aBasketProducts as $oContent ) {
                    $sId = $oContent->getProductId();
                    $oProduct = oxNew('oxarticle');
                    $oProduct->load($sId);
                    //$sPath = $this->_getDeepestCategoryPath( $oProduct );
                    $sPath = $this->_getBasketProductCatPath( $oProduct );
                    $aBasket[] = $this->_convProd2EmosItem( $oProduct, $sPath, $oContent->getAmount() );
                }
                $oEmos->addEmosBasketPageArray( $aBasket );
                break;
            case 'oxwarticledetails':
                if ( $oProduct ) {
                    //$oEmos->addContent( 'Shop/'.$this->_getEmosCatPath().'/'.strip_tags( $oProduct->oxarticles__oxtitle->value ) );
                    //$sPath = $this->_getDeepestCategoryPath( $oProduct );
                    // #1939: econda category broken after search
                    $sPath = $this->_getBasketProductCatPath( $oProduct );
                    $sTitle = $this->_prepareProductTitle( $oProduct );
                    $oEmos->addContent( "Shop/{$sPath}/".strip_tags( $sTitle ) );
                    $oEmos->addDetailView( $this->_convProd2EmosItem( $oProduct, $sPath, 1 ) );
                }
                break;
            case 'search':
                $oEmos->addContent( 'Shop/Suche' );
                $iPage = oxConfig::getParameter( 'pgNr' );
                if ( !$iPage ) {
                    //ECONDA FIX only track first search page, not the following pages
                    // #1184M - specialchar search
                    //$sSearchParamForLink = rawurlencode( oxConfig::getParameter( 'searchparam', true ) );
                    // #4018: The emospro.search string is URL-encoded forwarded to econda instead of URL-escaped
                    $sSearchParamForLink =  oxConfig::getParameter( 'searchparam', true );
                    //$sOutput .= $oEmos->addSearch( $sSearchParamForLink, $oSmarty->_tpl_vars['d']->iArtCnt );
                    $iSearchCount = 0;
                    if (($oSmarty->_tpl_vars['oView']) && $oSmarty->_tpl_vars['oView']->getArticleCount()) {
                        $iSearchCount = $oSmarty->_tpl_vars['oView']->getArticleCount();
                    }
                    $sOutput .= $oEmos->addSearch( $sSearchParamForLink, $iSearchCount);
                }
                break;
            case 'alist':
                $oEmos->addContent( 'Shop/'.$this->_getEmosCatPath() );
                break;
            case 'account_wishlist':
                $oEmos->addContent( 'Service/Wunschzettel' );
                break;
            case 'contact':
                // #4042: Contact page is erroneously tracked as contact event
                if ( $oCurrView->getContactSendStatus() ) {
                    $oEmos->addContent( 'Service/Kontakt/Success' );
                    $oEmos->addContact( 'Kontakt' );                    
                }
                else {
                    $oEmos->addContent( 'Service/Kontakt/Form' );
                }
                break;
            case 'help':
                $oEmos->addContent( 'Service/Hilfe' );
                break;
            case 'newsletter':
                $oEmos->addContent( $oCurrView->getNewsletterStatus() ? 'Service/Newsletter/Success' : 'Service/Newsletter/Form' );
                break;
            case 'guestbook':
                $oEmos->addContent( 'Service/Gaestebuch' );
                break;
            case 'links':
                $oEmos->addContent( 'Service/Links' );
                break;
            case 'info':
                switch ( $sTplName ) {
                    case 'impressum.tpl':
                        $oEmos->addContent( 'Info/Impressum' );
                        break;
                    case 'agb.tpl':
                        $oEmos->addContent( 'Info/AGB' );
                        break;
                    case 'order_info.tpl':
                        $oEmos->addContent( 'Info/Bestellinfo' );
                        break;
                    case 'delivery_info.tpl':
                        $oEmos->addContent( 'Info/Versandinfo' );
                        break;
                    case 'security_info.tpl':
                        $oEmos->addContent( 'Info/Sicherheit' );
                        break;
                    default:
                        $oEmos->addContent( 'Content/'.$oStr->preg_replace( '/\.tpl$/', '', $sTplName ) );
                        break;
                }
                break;
            case 'account':
                if ( $sFnc ) {
                    $oEmos->addContent( ( $sFnc != 'logout' ) ? 'Login/Uebersicht' : 'Login/Formular/Logout' );
                } else {
                    $oEmos->addContent( 'Login/Formular/Login' );
                }
                break;
            case 'account_user':
                $oEmos->addContent( 'Login/Kundendaten' );
                break;
            case 'account_order':
                $oEmos->addContent( 'Login/Bestellungen' );
                break;
            case 'account_noticelist':
                $oEmos->addContent( 'Login/Merkzettel' );
                break;
            case 'account_newsletter':
                $oEmos->addContent( 'Login/Newsletter' );
                break;
            case 'account_whishlist':
                $oEmos->addContent( 'Login/Wunschzettel' );
                break;
            case 'forgotpassword':
                $oEmos->addContent( 'Login/PW vergessen' );
                break;
            case 'content':
                // backwards compatibility
                $oContent = ( $oCurrView instanceof content ) ? $oCurrView->getContent() : null;
                $sContentId = $oContent ? $oContent->oxcontents__oxloadid->value : null;
                switch ( $sContentId ) {
                    case 'oximpressum':
                        $oEmos->addContent( 'Info/Impressum' );
                        break;
                    case 'oxagb':
                        $oEmos->addContent( 'Info/AGB' );
                        break;
                    case 'oxorderinfo':
                        $oEmos->addContent( 'Info/Bestellinfo' );
                        break;
                    case 'oxdeliveryinfo':
                        $oEmos->addContent( 'Info/Versandinfo' );
                        break;
                    case 'oxsecurityinfo':
                        $oEmos->addContent( 'Info/Sicherheit' );
                        break;
                    default:
                        $oEmos->addContent( 'Content/'.$this->_getEmosPageTitle( $aParams ) );
                        break;
                }
                break;
            case 'register':

                $oEmos->addContent( 'Service/Register' );

                $iError   = oxConfig::getParameter( 'newslettererror' );
                $iSuccess = oxConfig::getParameter( 'success' );

                if ( $iError && $iError < 0 ) {
                    $oEmos->addRegister( $oUser ? $oUser->getId() : 'NULL', abs( $iError ) );
                }

                if ( $iSuccess && $iSuccess > 0 && $oUser ) {
                    $oEmos->addRegister( $oUser->getId(), 0 );
                }

                break;
            default:
                $oEmos->addContent( 'Content/'.$oStr->preg_replace( '/\.tpl$/', '', $sTplName ) );
                break;
        }

        // get the last Call for special handling function "tobasket", "changebasket"
        if ( ( $aLastCall = oxSession::getVar( 'aLastcall' ) ) ) {
            oxSession::deleteVar( 'aLastcall' );
        }

        // ADD To Basket and Remove from Basket
        if ( is_array( $aLastCall ) && count( $aLastCall ) ) {
            $sCallAction = key( $aLastCall );
            $aCallData   = current( $aLastCall );

            switch ( $sCallAction ) {
                case 'changebasket':
                    foreach ( $aCallData as $sItemId => $aItemData ) {
                        $oProduct = oxNew( 'oxarticle' );
                        if ( $aItemData['oldam'] > $aItemData['am'] && $oProduct->load( $aItemData['aid'] ) ) {
                            //ECONDA FIX always use the main category
                            //$sPath = $this->_getDeepestCategoryPath( $oProduct );
                            $sPath = $this->_getBasketProductCatPath( $oProduct );
                            $oEmos->removeFromBasket( $this->_convProd2EmosItem( $oProduct, $sPath, ( $aItemData['oldam'] - $aItemData['am'] ) ) );
                            //$oEmos->appendPreScript($aItemData['oldam'].'->'.$aItemData['am'].':'.$oProduct->load( $aItemData['aid']));
                        } elseif ( $aItemData['oldam'] < $aItemData['am'] && $oProduct->load( $aItemData['aid'] )) {
                            $sPath = $this->_getBasketProductCatPath( $oProduct );
                            $oEmos->addToBasket( $this->_convProd2EmosItem( $oProduct, $sPath, $aItemData['am'] -  $aItemData['oldam']) );
                        }
                    }
                    break;
                case 'tobasket':
                    foreach ( $aCallData as $sItemId => $aItemData ) {
                        // ECONDA FIX if there is a "add to basket" in the artcle list view, we do not have a product ID here
                        $oProduct = oxNew( 'oxarticle' );
                        if ( $oProduct->load( $sItemId ) ) {
                            //ECONDA FIX always use the main category
                            //$sPath = $this->_getDeepestCategoryPath( $oProduct );
                            $sPath = $this->_getBasketProductCatPath( $oProduct );
                            $oEmos->addToBasket( $this->_convProd2EmosItem( $oProduct, $sPath, $aItemData['am'] ) );
                        }
                    }
                    break;
            }
        }

        // track logins
        if ( 'login_noredirect' == $sFnc ) {
            $oEmos->addLogin( oxConfig::getParameter( 'lgn_usr' ), $oUser ? '0' : '1' );
        }

        return "\n".$oEmos->toString();
    }
}
