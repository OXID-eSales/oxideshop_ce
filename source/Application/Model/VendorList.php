<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxField;

/**
 * Vendor list manager.
 * Collects list of vendors according to collection rules (activ, etc.).
 *
 */
class VendorList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Vendor root.
     *
     * @var \stdClass
     */
    protected $_oRoot = null;

    /**
     * Vendor tree path.
     *
     * @var array
     */
    protected $_aPath = [];

    /**
     * To show vendor article count or not
     *
     * @var bool
     */
    protected $_blShowVendorArticleCnt = false;

    /**
     * Active vendor object
     *
     * @var \OxidEsales\Eshop\Application\Model\Vendor
     */
    protected $_oClickedVendor = null;

    /**
     * Calls parent constructor and defines if Article vendor count is shown
     */
    public function __construct()
    {
        $this->setShowVendorArticleCnt($this->getConfig()->getConfigParam('bl_perfShowActionCatArticleCnt'));
        parent::__construct('oxvendor');
    }

    /**
     * Enables/disables vendor article count calculation
     *
     * @param bool $blShowVendorArticleCnt to show article count or not
     */
    public function setShowVendorArticleCnt($blShowVendorArticleCnt = false)
    {
        $this->_blShowVendorArticleCnt = $blShowVendorArticleCnt;
    }

    /**
     * Loads simple vendor list
     */
    public function loadVendorList()
    {
        $oBaseObject = $this->getBaseObject();
        $sFieldList = $oBaseObject->getSelectFields();
        $sViewName = $oBaseObject->getViewName();
        $this->getBaseObject()->setShowArticleCnt($this->_blShowVendorArticleCnt);

        $sWhere = '';
        if (!$this->isAdmin()) {
            $sWhere = $oBaseObject->getSqlActiveSnippet();
            $sWhere = $sWhere ? " where $sWhere and " : ' where ';
            $sWhere .= "{$sViewName}.oxtitle != '' ";
        }

        $sSelect = "select {$sFieldList} from {$sViewName} {$sWhere} order by {$sViewName}.oxtitle";
        $this->selectString($sSelect);
    }

    /**
     * Creates fake root for vendor tree, and ads category list fileds for each vendor item
     *
     * @param string $sLinkTarget  Name of class, responsible for category rendering
     * @param string $sActCat      Active category
     * @param string $sShopHomeUrl base shop url ($myConfig->getShopHomeUrl())
     */
    public function buildVendorTree($sLinkTarget, $sActCat, $sShopHomeUrl)
    {
        $sActCat = str_replace('v_', '', $sActCat);

        //Load vendor list
        $this->loadVendorList();


        //Create fake vendor root category
        $this->_oRoot = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
        $this->_oRoot->load('root');

        //category fields
        $this->_addCategoryFields($this->_oRoot);
        $this->_aPath[] = $this->_oRoot;

        foreach ($this as $sVndId => $oVendor) {
            // storing active vendor object
            if ($sVndId == $sActCat) {
                $this->setClickVendor($oVendor);
            }

            $this->_addCategoryFields($oVendor);
            if ($sActCat == $oVendor->oxvendor__oxid->value) {
                $this->_aPath[] = $oVendor;
            }
        }

        $this->_seoSetVendorData();
    }

    /**
     * Root vendor list node (which usually is a manually prefilled object) getter
     *
     * @return \OxidEsales\Eshop\Application\Model\Vendor
     */
    public function getRootCat()
    {
        return $this->_oRoot;
    }

    /**
     * Returns vendor path array
     *
     * @return array
     */
    public function getPath()
    {
        return $this->_aPath;
    }

    /**
     * Adds category specific fields to vendor object
     *
     * @param object $oVendor vendor object
     */
    protected function _addCategoryFields($oVendor)
    {
        $oVendor->oxcategories__oxid = new \OxidEsales\Eshop\Core\Field("v_" . $oVendor->oxvendor__oxid->value);
        $oVendor->oxcategories__oxicon = $oVendor->oxvendor__oxicon;
        $oVendor->oxcategories__oxtitle = $oVendor->oxvendor__oxtitle;
        $oVendor->oxcategories__oxdesc = $oVendor->oxvendor__oxshortdesc;

        $oVendor->setIsVisible(true);
        $oVendor->setHasVisibleSubCats(false);
    }

    /**
     * Sets active (open) vendor object
     *
     * @param \OxidEsales\Eshop\Application\Model\Vendor $oVendor active vendor
     */
    public function setClickVendor($oVendor)
    {
        $this->_oClickedVendor = $oVendor;
    }

    /**
     * returns active (open) vendor object
     *
     * @return \OxidEsales\Eshop\Application\Model\Vendor
     */
    public function getClickVendor()
    {
        return $this->_oClickedVendor;
    }

    /**
     * Processes vendor category URLs
     */
    protected function _seoSetVendorData()
    {
        // only when SEO id on and in front end
        if (\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive() && !$this->isAdmin()) {
            $oEncoder = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class);

            // preparing root vendor category
            if ($this->_oRoot) {
                $oEncoder->getVendorUrl($this->_oRoot);
            }

            // encoding vendor category
            foreach ($this as $sVndId => $value) {
                $oEncoder->getVendorUrl($this->_aArray[$sVndId]);
            }
        }
    }
}
