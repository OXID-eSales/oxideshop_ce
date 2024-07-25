<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use stdClass;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

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

    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sSLViewName = $tableViewNameGenerator->getViewName('oxselectlist');

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
                $oArticle->loadInLang(key($oOtherLang), $soxId);
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }

            if ($oArticle->oxarticles__oxparentid->value) {
                $this->_aViewData["parentarticle"] = $this->getProductParent($oArticle->oxarticles__oxparentid->value);
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

        return "article_variant";
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
            $sOXID = Registry::getRequest()->getRequestEscapedParameter("voxid");
            $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");
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

        if (!$this->isAnythingChanged($oArticle, $aParams)) {
            return;
        }

        $oArticle->setLanguage(0);
        $oArticle->assign($aParams);
        $oArticle->setLanguage($this->_iEditLang);

        // #0004473
        $oArticle->resetRemindStatus();

        if ($sOXID == "-1") {
            if ($oParent = $this->getProductParent($oArticle->oxarticles__oxparentid->value)) {
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
    protected function isAnythingChanged($oProduct, $aData)
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
    protected function getProductParent($sParentId)
    {
        if (
            $this->_oProductParent === null ||
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
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");
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

        $variantOxid = Registry::getRequest()->getRequestParameter("voxid");
        $variant = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $variant->delete($variantOxid);
    }

    /**
     * Changes name of variant.
     */
    public function changename()
    {
        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

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

            if ($aSels = Registry::getRequest()->getRequestEscapedParameter("allsel")) {
                $oVariantHandler = oxNew(\OxidEsales\Eshop\Application\Model\VariantHandler::class);
                $oVariantHandler->genVariantFromSell($aSels, $oArticle);
            }
        }
    }
}
