<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use function basename;

/**
 * CMS - loads pages and displays it
 */
class ContentController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Content id.
     *
     * @var string
     */
    protected $_sContentId = null;

    /**
     * Content object
     *
     * @var object
     */
    protected $_oContent = null;

    /**
     * Current view template
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/info/content';

    /**
     * Current view plain template
     *
     * @var string
     */
    protected $_sThisPlainTemplate = 'page/info/content_plain';

    /**
     * Current view content category (if available)
     */
    protected $_oContentCat = null;

    /**
     * Ids of contents which can be accessed without any restrictions when private sales is ON
     *
     * @var array
     */
    protected $_aPsAllowedContents = ["oxagb", "oxrightofwithdrawal", "oximpressum"];

    /**
     * Current view content title
     *
     * @var string
     */
    protected $_sContentTitle = null;

    /**
     * Sign if to load and show bargain action
     *
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Business entity data template
     *
     * @var string
     */
    protected $_sBusinessTemplate = 'rdfa/content/inc/business_entity';

    /**
     * Delivery charge data template
     *
     * @var string
     */
    protected $_sDeliveryTemplate = 'rdfa/content/inc/delivery_charge';

    /**
     * Payment charge data template
     *
     * @var string
     */
    protected $_sPaymentTemplate = 'rdfa/content/inc/payment_charge';

    /**
     * An array including all ShopConfVars which are used to extend business
     * entity data
     *
     * @var array
     */
    protected $_aBusinessEntityExtends = [
        'sRDFaLogoUrl',
        'sRDFaLongitude',
        'sRDFaLatitude',
        'sRDFaGLN',
        'sRDFaNAICS',
        'sRDFaISIC',
        'sRDFaDUNS',
    ];

    /**
     * Returns prefix ID used by template engine.
     *
     * @return string    $this->_sViewId
     */
    public function getViewId()
    {
        if (!isset($this->_sViewId)) {
            $this->_sViewId = parent::getViewId() . '|' . Registry::getRequest()->getRequestEscapedParameter('oxcid');
        }

        return $this->_sViewId;
    }

    /** @inheritdoc  */
    public function render()
    {
        parent::render();

        $content = $this->getContent();
        if ($content && $content->getLoadId()) {
            $this->validateContentAccessPermissions($content->getLoadId());
            $this->getViewConfig()->setViewConfigParam('oxloadid', $content->getLoadId());
        }
        $templateName = $this->getTplName();
        if (!$templateName && (!$content || !$content->getId())) {
            error_404_handler();
        }
        if ($this->showPlainTemplate()) {
            $this->_sThisTemplate = $this->_sThisPlainTemplate;
        } elseif ($templateName) {
            $this->_sThisTemplate = $templateName;
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
    protected function canShowContent($sContentIdent)
    {
        return !(
            $this->isEnabledPrivateSales() &&
            !$this->getUser() && !in_array($sContentIdent, $this->_aPsAllowedContents)
        );
    }

    /**
     * Returns current view meta data
     * If $sMeta parameter comes empty, sets to it current content title
     *
     * @param string $sMeta     category path
     * @param int    $iLength   max length of result, -1 for no truncation
     * @param bool   $blDescTag if true - performs additional duplicate cleaning
     *
     * @return string
     */
    protected function prepareMetaDescription($sMeta, $iLength = 200, $blDescTag = false)
    {
        if (!$sMeta) {
            $sMeta = $this->getContent()->oxcontents__oxtitle->value;
        }

        return parent::prepareMetaDescription($sMeta, $iLength, $blDescTag);
    }

    /**
     * Returns current view keywords seperated by comma
     * If $sKeywords parameter comes empty, sets to it current content title
     *
     * @param string $sKeywords               data to use as keywords
     * @param bool   $blRemoveDuplicatedWords remove duplicated words
     *
     * @return string
     */
    protected function prepareMetaKeyword($sKeywords, $blRemoveDuplicatedWords = true)
    {
        if (!$sKeywords) {
            $sKeywords = $this->getContent()->oxcontents__oxtitle->value;
        }

        return parent::prepareMetaKeyword($sKeywords, $blRemoveDuplicatedWords);
    }

    /**
     * If current content is assigned to category returns its object
     *
     * @return \OxidEsales\Eshop\Application\Model\Content
     */
    public function getContentCategory()
    {
        if ($this->_oContentCat === null) {
            // setting default status ..
            $this->_oContentCat = false;
            if (($oContent = $this->getContent()) && $oContent->oxcontents__oxtype->value == 2) {
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
        $blPlain = (bool) Registry::getRequest()->getRequestEscapedParameter('plain');
        if ($blPlain === false) {
            $oUser = $this->getUser();
            if (
                $this->isEnabledPrivateSales() &&
                (!$oUser || ($oUser && !$oUser->isTermsAccepted()))
            ) {
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
    protected function getSeoObjectId()
    {
        return Registry::getRequest()->getRequestEscapedParameter('oxcid');
    }

    /**
     * Template variable getter. Returns active content id.
     * If no content id specified, uses "impressum" content id
     *
     * @return object
     */
    public function getContentId()
    {
        if ($this->_sContentId === null) {
            $sContentId = Registry::getRequest()->getRequestEscapedParameter('oxcid');
            $sLoadId = Registry::getRequest()->getRequestEscapedParameter('oxloadid');

            $this->_sContentId = false;
            $oContent = oxNew(\OxidEsales\Eshop\Application\Model\Content::class);

            if ($sLoadId) {
                $blRes = $oContent->loadByIdent($sLoadId);
            } elseif ($sContentId) {
                $blRes = $oContent->load($sContentId);
            } else {
                //get default content (impressum)
                $blRes = $oContent->loadByIdent('oximpressum');
            }

            if ($blRes && $oContent->oxcontents__oxactive->value) {
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
        if ($this->_oContent === null) {
            $this->_oContent = false;
            if ($this->getContentId()) {
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
    protected function getSubject($iLang)
    {
        return $this->getContent();
    }

    /**
     * Returns name of template
     *
     * @return string
     */
    protected function getTplName()
    {
        $requestedTemplate = Registry::getRequest()->getRequestEscapedParameter('tpl');
        if (!$requestedTemplate) {
            return null;
        }
        // security fix so that you can't access files from outside template dir
        $baseName = basename($requestedTemplate);
        return "message/$baseName";
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $oContent = $this->getContent();

        $aPaths = [];
        $aPath = [];

        $aPath['title'] = $oContent->oxcontents__oxtitle->value;
        $aPath['link'] = $this->getLink();
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
        if ($this->_sContentTitle === null) {
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
        return Registry::getConfig()->getConfigParam('blRDFaEmbedding');
    }

    /**
     * Returns template name wich content page to specify:
     * business entity data, payment charge specifications or delivery charge
     *
     * @return array
     */
    public function getContentPageTpl()
    {
        $aTemplate = [];
        $sContentId = $this->getContent()->oxcontents__oxloadid->value;
        $myConfig = Registry::getConfig();
        if ($sContentId == $myConfig->getConfigParam('sRDFaBusinessEntityLoc')) {
            $aTemplate[] = $this->_sBusinessTemplate;
        }
        if ($sContentId == $myConfig->getConfigParam('sRDFaDeliveryChargeSpecLoc')) {
            $aTemplate[] = $this->_sDeliveryTemplate;
        }
        if ($sContentId == $myConfig->getConfigParam('sRDFaPaymentChargeSpecLoc')) {
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
        $myConfig = Registry::getConfig();
        $aExtends = [];

        foreach ($this->_aBusinessEntityExtends as $sExtend) {
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
        $oPayments = oxNew(\OxidEsales\Eshop\Application\Model\PaymentList::class);
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
        $oDelSets = oxNew(\OxidEsales\Eshop\Application\Model\DeliverySetList::class);
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
        $aDeliveryChargeSpecs = [];
        $oDeliveryChargeSpecs = $this->getDeliveryList();
        foreach ($oDeliveryChargeSpecs as $oDeliveryChargeSpec) {
            if ($oDeliveryChargeSpec->oxdelivery__oxaddsumtype->value == "abs") {
                $oDelSets = oxNew(\OxidEsales\Eshop\Application\Model\DeliverySetList::class);
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
        if ($this->_oDelList === null) {
            $this->_oDelList = oxNew(\OxidEsales\Eshop\Application\Model\DeliveryList::class);
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
        return Registry::getConfig()->getConfigParam('iRDFaVAT');
    }

    /**
     * Returns rdfa VAT
     *
     * @return bool
     */
    public function getRdfaPriceValidity()
    {
        $iDays = Registry::getConfig()->getConfigParam('iRDFaPriceValidity');
        $iFrom = Registry::getUtilsDate()->getTime();
        $iThrough = $iFrom + ($iDays * 24 * 60 * 60);
        $oPriceValidity = [];
        $oPriceValidity['validfrom'] = date('Y-m-d\TH:i:s', $iFrom) . "Z";
        $oPriceValidity['validthrough'] = date('Y-m-d\TH:i:s', $iThrough) . "Z";

        return $oPriceValidity;
    }

    /**
     * Returns content parsed through renderer
     *
     * @return string
     */
    public function getParsedContent()
    {
        $activeView = oxNew(FrontendController::class);
        $activeView->addGlobalParams();
        $activeLanguageId = Registry::getLang()->getTplLanguage();
        return $this->getRenderer()->renderFragment(
            $this->getContent()->oxcontents__oxcontent->value,
            "ox:{$this->getContent()->getId()}{$activeLanguageId}",
            $activeView->getViewData()
        );
    }

    private function getRenderer(): TemplateRendererInterface
    {
        return $this->getContainer()->get(TemplateRendererBridgeInterface::class)->getTemplateRenderer();
    }

    /**
     * Returns view canonical url
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        $url = '';
        if ($content = $this->getContent()) {
            $utils = Registry::getUtilsUrl();
            if (Registry::getUtils()->seoIsActive()) {
                $url = $utils->prepareCanonicalUrl($content->getBaseSeoLink($content->getLanguage()));
            } else {
                $url = $utils->prepareCanonicalUrl($content->getBaseStdLink($content->getLanguage()));
            }
        }

        return $url;
    }

    /**
     * Terminates execution with exit() on no permissions
     * @param string $contentId
     * @return void
     */
    private function validateContentAccessPermissions(string $contentId): void
    {
        if (!$this->canShowContent($contentId)) {
            Registry::getUtils()->redirect(Registry::getConfig()->getShopHomeUrl() . 'cl=account');
        }
    }
}
