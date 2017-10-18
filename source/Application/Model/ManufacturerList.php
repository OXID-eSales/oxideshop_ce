<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxField;

/**
 * Manufacturer list manager.
 * Collects list of manufacturers according to collection rules (activ, etc.).
 *
 */
class ManufacturerList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Manufacturer root.
     *
     * @var \stdClass
     */
    protected $_oRoot = null;

    /**
     * Manufacturer tree path.
     *
     * @var array
     */
    protected $_aPath = [];

    /**
     * To show manufacturer article count or not
     *
     * @var bool
     */
    protected $_blShowManufacturerArticleCnt = false;

    /**
     * Active manufacturer object
     *
     * @var \OxidEsales\Eshop\Application\Model\Manufacturer
     */
    protected $_oClickedManufacturer = null;

    /**
     * Calls parent constructor and defines if Article vendor count is shown
     */
    public function __construct()
    {
        $this->setShowManufacturerArticleCnt($this->getConfig()->getConfigParam('bl_perfShowActionCatArticleCnt'));
        parent::__construct('oxmanufacturer');
    }

    /**
     * Enables/disables manufacturer article count calculation
     *
     * @param bool $blShowManufacturerArticleCnt to show article count or not
     */
    public function setShowManufacturerArticleCnt($blShowManufacturerArticleCnt = false)
    {
        $this->_blShowManufacturerArticleCnt = $blShowManufacturerArticleCnt;
    }

    /**
     * Loads simple manufacturer list
     */
    public function loadManufacturerList()
    {
        $oBaseObject = $this->getBaseObject();

        $sFieldList = $oBaseObject->getSelectFields();
        $sViewName = $oBaseObject->getViewName();
        $this->getBaseObject()->setShowArticleCnt($this->_blShowManufacturerArticleCnt);

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
     * Creates fake root for manufacturer tree, and ads category list fileds for each manufacturer item
     *
     * @param string $sLinkTarget  Name of class, responsible for category rendering
     * @param string $sActCat      Active category
     * @param string $sShopHomeUrl base shop url ($myConfig->getShopHomeUrl())
     */
    public function buildManufacturerTree($sLinkTarget, $sActCat, $sShopHomeUrl)
    {
        //Load manufacturer list
        $this->loadManufacturerList();


        //Create fake manufacturer root category
        $this->_oRoot = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        $this->_oRoot->load("root");

        //category fields
        $this->_addCategoryFields($this->_oRoot);
        $this->_aPath[] = $this->_oRoot;

        foreach ($this as $sVndId => $oManufacturer) {
            // storing active manufacturer object
            if ((string)$sVndId === $sActCat) {
                $this->setClickManufacturer($oManufacturer);
            }

            $this->_addCategoryFields($oManufacturer);
            if ($sActCat == $oManufacturer->oxmanufacturers__oxid->value) {
                $this->_aPath[] = $oManufacturer;
            }
        }

        $this->_seoSetManufacturerData();
    }

    /**
     * Root manufacturer list node (which usually is a manually prefilled object) getter
     *
     * @return \OxidEsales\Eshop\Application\Model\Manufacturer
     */
    public function getRootCat()
    {
        return $this->_oRoot;
    }

    /**
     * Returns manufacturer path array
     *
     * @return array
     */
    public function getPath()
    {
        return $this->_aPath;
    }

    /**
     * Adds category specific fields to manufacturer object
     *
     * @param object $oManufacturer manufacturer object
     */
    protected function _addCategoryFields($oManufacturer)
    {
        $oManufacturer->oxcategories__oxid = new \OxidEsales\Eshop\Core\Field($oManufacturer->oxmanufacturers__oxid->value);
        $oManufacturer->oxcategories__oxicon = $oManufacturer->oxmanufacturers__oxicon;
        $oManufacturer->oxcategories__oxtitle = $oManufacturer->oxmanufacturers__oxtitle;
        $oManufacturer->oxcategories__oxdesc = $oManufacturer->oxmanufacturers__oxshortdesc;

        $oManufacturer->setIsVisible(true);
        $oManufacturer->setHasVisibleSubCats(false);
    }

    /**
     * Sets active (open) manufacturer object
     *
     * @param \OxidEsales\Eshop\Application\Model\Manufacturer $oManufacturer active manufacturer
     */
    public function setClickManufacturer($oManufacturer)
    {
        $this->_oClickedManufacturer = $oManufacturer;
    }

    /**
     * returns active (open) manufacturer object
     *
     * @return \OxidEsales\Eshop\Application\Model\Manufacturer
     */
    public function getClickManufacturer()
    {
        return $this->_oClickedManufacturer;
    }

    /**
     * Processes manufacturer category URLs
     */
    protected function _seoSetManufacturerData()
    {
        // only when SEO id on and in front end
        if (\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive() && !$this->isAdmin()) {
            $oEncoder = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class);

            // preparing root manufacturer category
            if ($this->_oRoot) {
                $oEncoder->getManufacturerUrl($this->_oRoot);
            }

            // encoding manufacturer category
            foreach ($this as $sVndId => $value) {
                $oEncoder->getManufacturerUrl($this->_aArray[$sVndId]);
            }
        }
    }
}
