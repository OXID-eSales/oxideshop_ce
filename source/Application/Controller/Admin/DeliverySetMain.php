<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use stdClass;

/**
 * Admin article main deliveryset manager.
 * There is possibility to change deliveryset name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main Sets.
 */
class DeliverySetMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $odeliveryset = oxNew(\OxidEsales\Eshop\Application\Model\DeliverySet::class);
            $odeliveryset->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $odeliveryset->getAvailableInLangs();

            if (!isset($oOtherLang[$this->_iEditLang])) {
                $odeliveryset->loadInLang(key($oOtherLang), $soxId);
            }

            $this->_aViewData["edit"] = $odeliveryset;
            //Disable editing for derived articles
            if ($odeliveryset->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
            // remove already created languages
            $aLang = array_diff(\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames(), $oOtherLang);
            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        if (Registry::getRequest()->getRequestEscapedParameter("aoc")) {
            $oDeliverysetMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetMainAjax::class);
            $this->_aViewData['oxajax'] = $oDeliverysetMainAjax->getColumns();

            return "popups/deliveryset_main";
        }

        return "deliveryset_main";
    }

    /**
     * Saves deliveryset information changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        $oDelSet = oxNew(\OxidEsales\Eshop\Application\Model\DeliverySet::class);

        if ($soxId != "-1") {
            $oDelSet->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxdeliveryset__oxid'] = null;
        }

        // checkbox handling
        if (!isset($aParams['oxdeliveryset__oxactive'])) {
            $aParams['oxdeliveryset__oxactive'] = 0;
        }

        //Disable editing for derived articles
        if ($oDelSet->isDerived()) {
            return;
        }

        //$aParams = $oDelSet->ConvertNameArray2Idx( $aParams);
        $oDelSet->setLanguage(0);
        $oDelSet->assign($aParams);
        $oDelSet->setLanguage($this->_iEditLang);
        $oDelSet = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oDelSet);
        $oDelSet->save();

        // set oxid if inserted
        $this->setEditObjectId($oDelSet->getId());
    }

    /**
     * Saves deliveryset data to different language (eg. english).
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxdeliveryset__oxactive'])) {
            $aParams['oxdeliveryset__oxactive'] = 0;
        }

        $oDelSet = oxNew(\OxidEsales\Eshop\Application\Model\DeliverySet::class);

        if ($soxId != "-1") {
            $oDelSet->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxdeliveryset__oxid'] = null;
            //$aParams = $oDelSet->ConvertNameArray2Idx( $aParams);
        }

        $oDelSet->setLanguage(0);
        $oDelSet->assign($aParams);

        //Disable editing for derived articles
        if ($oDelSet->isDerived()) {
            return;
        }

        // apply new language
        $oDelSet->setLanguage(Registry::getRequest()->getRequestEscapedParameter("new_lang"));
        $oDelSet->save();

        // set oxid if inserted
        $this->setEditObjectId($oDelSet->getId());
    }
}
