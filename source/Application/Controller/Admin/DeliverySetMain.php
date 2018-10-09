<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;

/**
 * Admin article main deliveryset manager.
 * There is possibility to change deliveryset name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main Sets.
 */
class DeliverySetMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates deliveryset category tree,
     * passes data to Smarty engine and returns name of template file "deliveryset_main.tpl".
     *
     * @return string
     */
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
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
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

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oDeliverysetMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetMainAjax::class);
            $this->_aViewData['oxajax'] = $oDeliverysetMainAjax->getColumns();

            return "popups/deliveryset_main.tpl";
        }

        return "deliveryset_main.tpl";
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
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

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
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
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
        $oDelSet->setLanguage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("new_lang"));
        $oDelSet->save();

        // set oxid if inserted
        $this->setEditObjectId($oDelSet->getId());
    }
}
