<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxField;
use stdClass;

/**
 * Admin article variants manager.
 * Collects and updates article variants data.
 * Admin Menu: Manage Products -> Articles -> Variants.
 */
class ArticleVariant extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Variant parent product object
     *
     * @var \OxidEsales\Eshop\Application\Model\Article
     */
    protected $_oProductParent = null;

    /**
     * Loads article variants data, passes it to Smarty engine and returns name of
     * template file "article_variant.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        $sSLViewName = getViewName('oxselectlist');

        // all selectlists
        $oAllSel = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oAllSel->init("oxselectlist");
        $sQ = "select * from $sSLViewName";
        $oAllSel->selectString($sQ);
        $this->_aViewData["allsel"] = $oAllSel;

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $this->_aViewData["edit"] = $oArticle;

        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oArticle->loadInLang($this->_iEditLang, $soxId);

            if ($oArticle->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            $_POST["language"] = $_GET["language"] = $this->_iEditLang;
            $oVariants = $oArticle->getAdminVariants($this->_iEditLang);

            $this->_aViewData["mylist"] = $oVariants;

            // load object in other languages
            $oOtherLang = $oArticle->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oArticle->loadInLang(key($oOtherLang), $soxId);
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }

            if ($oArticle->oxarticles__oxparentid->value) {
                $this->_aViewData["parentarticle"] = $this->_getProductParent($oArticle->oxarticles__oxparentid->value);
                $this->_aViewData["oxparentid"] = $oArticle->oxarticles__oxparentid->value;
                $this->_aViewData["issubvariant"] = 1;
                // A. disable variant information editing for variant
                $this->_aViewData["readonly"] = 1;
            }
            $this->_aViewData["editlanguage"] = $this->_iEditLang;

            $aLang = array_diff(\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames(), $oOtherLang);
            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = $oLang;
            }
        }

        return "article_variant.tpl";
    }

    /**
     * Saves article variant.
     *
     * @param string $sOXID   Object ID
     * @param array  $aParams Parameters
     *
     * @return null
     */
    public function savevariant($sOXID = null, $aParams = null)
    {
        if (!isset($sOXID) && !isset($aParams)) {
            $sOXID = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("voxid");
            $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        }

        // varianthandling
        $soxparentId = $this->getEditObjectId();
        if (isset($soxparentId) && $soxparentId && $soxparentId != "-1") {
            $aParams['oxarticles__oxparentid'] = $soxparentId;
        } else {
            unset($aParams['oxarticles__oxparentid']);
        }
        /** @var \OxidEsales\Eshop\Application\Model\Article $oArticle */
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        if ($sOXID != "-1") {
            $oArticle->loadInLang($this->_iEditLang, $sOXID);
        }

        // checkbox handling
        if (is_array($aParams) && !isset($aParams['oxarticles__oxactive'])) {
            $aParams['oxarticles__oxactive'] = 0;
        }

        if (!$this->_isAnythingChanged($oArticle, $aParams)) {
            return;
        }

        $oArticle->setLanguage(0);
        $oArticle->assign($aParams);
        $oArticle->setLanguage($this->_iEditLang);

        // #0004473
        $oArticle->resetRemindStatus();

        if ($sOXID == "-1") {
            if ($oParent = $this->_getProductParent($oArticle->oxarticles__oxparentid->value)) {
                // assign field from parent for new variant
                // #4406
                $oArticle->oxarticles__oxisconfigurable = new \OxidEsales\Eshop\Core\Field($oParent->oxarticles__oxisconfigurable->value);
                $oArticle->oxarticles__oxremindactive = new \OxidEsales\Eshop\Core\Field($oParent->oxarticles__oxremindactive->value);
            }
        }

        $oArticle->save();
    }

    /**
     * Checks if anything is changed in given data compared with existing product values.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oProduct Product to be checked.
     * @param array                                       $aData    Data provided for check.
     *
     * @return bool
     */
    protected function _isAnythingChanged($oProduct, $aData)
    {
        if (!is_array($aData)) {
            return true;
        }
        foreach ($aData as $sKey => $sValue) {
            if (isset($oProduct->$sKey) && $oProduct->$sKey->value != $aData[$sKey]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns variant parent object
     *
     * @param string $sParentId parent product id
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    protected function _getProductParent($sParentId)
    {
        if ($this->_oProductParent === null ||
            ($this->_oProductParent !== false && $this->_oProductParent->getId() != $sParentId)
        ) {
            $this->_oProductParent = false;
            $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            if ($oProduct->load($sParentId)) {
                $this->_oProductParent = $oProduct;
            }
        }

        return $this->_oProductParent;
    }

    /**
     * Saves all article variants at once.
     */
    public function savevariants()
    {
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        if (is_array($aParams)) {
            foreach ($aParams as $soxId => $aVarParams) {
                $this->savevariant($soxId, $aVarParams);
            }
        }

        $this->resetContentCache();
    }

    /**
     * Deletes article variant.
     *
     * @return null
     */
    public function deleteVariant()
    {
        $editObjectOxid = $this->getEditObjectId();
        $editObject = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $editObject->load($editObjectOxid);
        if ($editObject->isDerived()) {
            return;
        }

        $this->resetContentCache();

        $variantOxid = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestRawParameter("voxid");
        $variant = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $variant->delete($variantOxid);
    }

    /**
     * Changes name of variant.
     */
    public function changename()
    {
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $this->resetContentCache();

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        if ($soxId != "-1") {
            $oArticle->loadInLang($this->_iEditLang, $soxId);
        }

        $oArticle->setLanguage(0);
        $oArticle->assign($aParams);
        $oArticle->setLanguage($this->_iEditLang);
        $oArticle->save();
    }


    /**
     * Add selection list
     *
     * @return null
     */
    public function addsel()
    {
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        if ($oArticle->load($this->getEditObjectId())) {
            //Disable editing for derived articles
            if ($oArticle->isDerived()) {
                return;
            }

            $this->resetContentCache();

            if ($aSels = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("allsel")) {
                $oVariantHandler = oxNew(\OxidEsales\Eshop\Application\Model\VariantHandler::class);
                $oVariantHandler->genVariantFromSell($aSels, $oArticle);
            }
        }
    }
}
