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
 * CMS - loads pages and displays it
 */
class Content extends oxUBase
{
    /**
     * Content id.
     * @var string
     */
    protected $_sContentId = null;

    /**
     * Content object
     * @var object
     */
    protected $_oContent = null;

    /**
     * Current view template
     * @var string
     */
    protected $_sThisTemplate = 'page/info/content.tpl';

    /**
     * Current view plain template
     * @var string
     */
    protected $_sThisPlainTemplate = 'page/info/content_plain.tpl';

    /**
     * Current view content category (if available)
     * @var oxcontent
     */
     protected $_oContentCat = null;

     /**
      * Ids of contents which can be accessed without any restrictions when private sales is ON
      * @var array
      */
     protected $_aPsAllowedContents = array( "oxagb", "oxrightofwithdrawal", "oximpressum" );

     /**
     * Current view content title
     * @var sting
     */
    protected $_sContentTitle = null;

    /**
     * Sign if to load and show bargain action
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Business entity data template
     * @var string
     */
    protected $_sBusinessTemplate = 'rdfa/content/inc/business_entity.tpl';

    /**
     * Delivery charge data template
     * @var string
     */
    protected $_sDeliveryTemplate = 'rdfa/content/inc/delivery_charge.tpl';

    /**
     * Payment charge data template
     * @var string
     */
    protected $_sPaymentTemplate = 'rdfa/content/inc/payment_charge.tpl';

    /**
    * An array including all ShopConfVars which are used to extend business
    * entity data
    *
    * @var array
    */
    protected $_aBusinessEntityExtends = array(    "sRDFaLogoUrl",
                                                "sRDFaLongitude",
                                                "sRDFaLatitude",
                                                "sRDFaGLN",
                                                "sRDFaNAICS",
                                                "sRDFaISIC",
                                                "sRDFaDUNS");

    /**
     * Returns prefix ID used by template engine.
     *
     * @return string    $this->_sViewId
     */
    public function getViewId()
    {
        if ( !isset( $this->_sViewId ) ) {
            $this->_sViewId = parent::getViewId().'|'.oxConfig::getParameter( 'oxcid' );
        }
        return $this->_sViewId;
    }

    /**
     * Executes parent::render(), passes template variables to
     * template engine and generates content. Returns the name
     * of template to render content::_sThisTemplate
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {
        parent::render();

        $oContent = $this->getContent();
        if ( $oContent && !$this->_canShowContent( $oContent->oxcontents__oxloadid->value ) ) {
            oxRegistry::getUtils()->redirect( $this->getConfig()->getShopHomeURL() . 'cl=account' );
        }

        $sTpl = false;
        if ( $sTplName = $this->_getTplName() ) {
            $this->_sThisTemplate = $sTpl = $sTplName;
        } elseif ( $oContent ) {
            $sTpl = $oContent->getId();
        }

        if ( !$sTpl ) {
            error_404_handler();
        }

        // sometimes you need to display plain templates (e.g. when showing popups)
        if ( $this->showPlainTemplate() ) {
            $this->_sThisTemplate = $this->_sThisPlainTemplate;
        }

        if ( $oContent ) {
            $this->getViewConfig()->setViewConfigParam( 'oxloadid', $oContent->getLoadId() );
        }

        return $this->_sThisTemplate;
    }

    /**
     * Checks if content can be shown
     *
     * @param string $sContentIdent ident of content to display
     *
     * @return bool
     */
    protected function _canShowContent( $sContentIdent )
    {
        $blCan = true;
        if ( $this->isEnabledPrivateSales() &&
             !$this->getUser() && !in_array( $sContentIdent, $this->_aPsAllowedContents ) ) {
            $blCan = false;
        }
        return $blCan;
    }

    /**
     * Returns current view meta data
     * If $sMeta parameter comes empty, sets to it current content title
     *
     * @param string $sMeta     category path
     * @param int    $iLength   max length of result, -1 for no truncation
     * @param bool   $blDescTag if true - performs additional dublicate cleaning
     *
     * @return string
     */
    protected function _prepareMetaDescription( $sMeta, $iLength = 200, $blDescTag = false )
    {
        if ( !$sMeta ) {
            $sMeta = $this->getContent()->oxcontents__oxtitle->value;
        }
        return parent::_prepareMetaDescription( $sMeta, $iLength, $blDescTag );
    }

    /**
     * Returns current view keywords seperated by comma
     * If $sKeywords parameter comes empty, sets to it current content title
     *
     * @param string $sKeywords               data to use as keywords
     * @param bool   $blRemoveDuplicatedWords remove dublicated words
     *
     * @return string
     */
    protected function _prepareMetaKeyword( $sKeywords, $blRemoveDuplicatedWords = true )
    {
        if ( !$sKeywords ) {
            $sKeywords = $this->getContent()->oxcontents__oxtitle->value;
        }
        return parent::_prepareMetaKeyword( $sKeywords, $blRemoveDuplicatedWords );
    }

    /**
     * If current content is assigned to category returns its object
     *
     * @return oxcontent
     */
    public function getContentCategory()
    {
        if ( $this->_oContentCat === null ) {
            // setting default status ..
            $this->_oContentCat = false;
            if ( ( $oContent = $this->getContent() ) && $oContent->oxcontents__oxtype->value == 2 ) {
                $this->_oContentCat = $oContent;
            }
        }
        return $this->_oContentCat;
    }

    /**
     * Returns true if user forces to display plain template or
     * if private sales switched ON and user is not logged in
     *
     * @return bool
     */
    public function showPlainTemplate()
    {
        $blPlain = (bool) oxConfig::getParameter( 'plain' );
        if ( $blPlain === false ) {
            $oUser = $this->getUser();
            if ( $this->isEnabledPrivateSales() &&
                 ( !$oUser || ( $oUser && !$oUser->isTermsAccepted() ) ) ) {
                $blPlain = true;
            }
        }

        return (bool) $blPlain;
    }

    /**
     * Returns active content id to load its seo meta info
     *
     * @return string
     */
    protected function _getSeoObjectId()
    {
        return oxConfig::getParameter( 'oxcid' );
    }

    /**
     * Template variable getter. Returns active content id.
     * If no content id specified, uses "impressum" content id
     *
     * @return object
     */
    public function getContentId()
    {
        if ( $this->_sContentId === null ) {

            $sContentId = oxConfig::getParameter( 'oxcid' );
            $sLoadId = oxConfig::getParameter( 'oxloadid' );

            $this->_sContentId = false;
            $oContent = oxNew( 'oxContent' );
            $blRes = false;

            if ( $sLoadId ) {
               $blRes = $oContent->loadByIdent( $sLoadId );
            } elseif ( $sContentId ) {
               $blRes = $oContent->load( $sContentId );
            } else {
                //get default content (impressum)
               $blRes = $oContent->loadByIdent( 'oximpressum' );
            }

            if ( $blRes && $oContent->oxcontents__oxactive->value ) {
                $this->_sContentId = $oContent->oxcontents__oxid->value;
                $this->_oContent = $oContent;
            }
        }

        return $this->_sContentId;
    }

    /**
     * Template variable getter. Returns active content
     *
     * @return object
     */
    public function getContent()
    {
        if ( $this->_oContent === null ) {
            $this->_oContent = false;
            if ( $this->getContentId() ) {
                return $this->_oContent;
            }
        }
        return $this->_oContent;
    }

    /**
     * returns object, assosiated with current view.
     * (the object that is shown in frontend)
     *
     * @param int $iLang language id
     *
     * @return object
     */
    protected function _getSubject( $iLang )
    {
        return $this->getContent();
    }

    /**
     * Returns name of template
     *
     * @return string
     */
    protected function _getTplName()
    {
        // assign template name
        $sTplName = oxConfig::getParameter( 'tpl');

        if ( $sTplName ) {
            // security fix so that you cant access files from outside template dir
            $sTplName = basename( $sTplName );

            //checking if it is template name, not content id
            if ( !getStr()->preg_match("/\.tpl$/", $sTplName) ) {
                $sTplName = null;
            } else {
                $sTplName = 'message/'.$sTplName;
            }
        }

        return $sTplName;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $oContent = $this->getContent();

        $aPaths = array();
        $aPath = array();

        $aPath['title'] = $oContent->oxcontents__oxtitle->value;
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Template variable getter. Returns tag title
     *
     * @return string
     */
    public function getTitle()
    {
        if ( $this->_sContentTitle === null ) {
            $oContent = $this->getContent();
            $this->_sContentTitle = $oContent->oxcontents__oxtitle->value;
        }

        return $this->_sContentTitle;
    }

    /**
     * Returns if page has rdfa
     *
     * @return bool
     */
    public function showRdfa()
    {
        return $this->getConfig()->getConfigParam( 'blRDFaEmbedding' );
    }

    /**
     * Returns template name wich content page to specify:
     * business entity data, payment charge specifications or delivery charge
     *
     * @return array
     */
    public function getContentPageTpl()
    {
        $aTemplate  = array();
        $sContentId = $this->getContent()->oxcontents__oxloadid->value;
        $myConfig   = $this->getConfig();
        if ( $sContentId == $myConfig->getConfigParam( 'sRDFaBusinessEntityLoc' )) {
            $aTemplate[] = $this->_sBusinessTemplate;
        }
        if ( $sContentId == $myConfig->getConfigParam( 'sRDFaDeliveryChargeSpecLoc' )) {
            $aTemplate[] = $this->_sDeliveryTemplate;
        }
        if ( $sContentId == $myConfig->getConfigParam( 'sRDFaPaymentChargeSpecLoc' )) {
            $aTemplate[] = $this->_sPaymentTemplate;
        }
        return $aTemplate;
    }

    /**
     * Gets extended business entity data
     *
     * @return object
     */
    public function getBusinessEntityExtends()
    {
        $myConfig = $this->getConfig();
        $aExtends = array();

        foreach ( $this->_aBusinessEntityExtends as $sExtend ) {
            $aExtends[$sExtend] = $myConfig->getConfigParam($sExtend);
        }

        return $aExtends;
    }

    /**
    * Returns an object including all payments which are not mapped to a
    * predefined GoodRelations payment method. This object is used for
    * defining new instances of gr:PaymentMethods at content pages.
    *
    * @return object
    */
    public function getNotMappedToRDFaPayments()
    {
        $oPayments = oxNew("oxPaymentList");
        $oPayments->loadNonRDFaPaymentList();
        return $oPayments;
    }

    /**
     * Returns an object including all delivery sets which are not mapped to a
     * predefined GoodRelations delivery method. This object is used for
     * defining new instances of gr:DeliveryMethods at content pages.
     *
     * @return object
     */
    public function getNotMappedToRDFaDeliverySets()
    {
        $oDelSets = oxNew("oxDeliverySetList");
        $oDelSets->loadNonRDFaDeliverySetList();
        return $oDelSets;
    }

    /**
     * Returns delivery methods with assigned deliverysets.
     *
     * @return object
     */
    public function getDeliveryChargeSpecs()
    {
        $aDeliveryChargeSpecs = array();
        $oDeliveryChargeSpecs = $this->getDeliveryList();
        foreach ($oDeliveryChargeSpecs as $oDeliveryChargeSpec) {
            if ($oDeliveryChargeSpec->oxdelivery__oxaddsumtype->value == "abs") {
                $oDelSets = oxNew("oxdeliverysetlist");
                $oDelSets->loadRDFaDeliverySetList($oDeliveryChargeSpec->getId());
                $oDeliveryChargeSpec->deliverysetmethods = $oDelSets;
                $aDeliveryChargeSpecs[] = $oDeliveryChargeSpec;
            }
        }
        return $aDeliveryChargeSpecs;
    }

    /**
     * Template variable getter. Returns delivery list
     *
     * @return object
     */
    public function getDeliveryList()
    {
        if ( $this->_oDelList === null ) {
            $this->_oDelList = oxNew( 'oxDeliveryList' );
            $this->_oDelList->getList();
        }
        return $this->_oDelList;
    }

    /**
     * Returns rdfa VAT
     *
     * @return bool
     */
    public function getRdfaVAT()
    {
        return $this->getConfig()->getConfigParam( 'iRDFaVAT' );
    }

    /**
     * Returns rdfa VAT
     *
     * @return bool
     */
    public function getRdfaPriceValidity()
    {
        $iDays = $this->getConfig()->getConfigParam( 'iRDFaPriceValidity' );
        $iFrom = oxRegistry::get("oxUtilsDate")->getTime();
        $iThrough = $iFrom + ($iDays * 24 * 60 * 60);
        $oPriceValidity = array();
        $oPriceValidity['validfrom'] = date('Y-m-d\TH:i:s', $iFrom)."Z";
        $oPriceValidity['validthrough'] = date('Y-m-d\TH:i:s', $iThrough)."Z";
        return $oPriceValidity;
    }

    /**
     * Returns content parsed through smarty
     *
     * @return string
     */
    public function getParsedContent()
    {
        return oxRegistry::get("oxUtilsView")->parseThroughSmarty( $this->getContent()->oxcontents__oxcontent->value, $this->getContent()->getId() );
    }

}
