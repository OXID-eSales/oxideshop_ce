<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;
use oxField;

/**
 * Admin article main delivery manager.
 * There is possibility to change delivery name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main.
 */
class DeliveryMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates delivery category tree,
     * passes data to Smarty engine and returns name of template file "delivery_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();

        // remove itm from list
        unset($this->_aViewData["sumtype"][2]);

        // Deliverytypes
        $aDelTypes = $this->getDeliveryTypes();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oDelivery = oxNew(\OxidEsales\Eshop\Application\Model\Delivery::class);
            $oDelivery->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oDelivery->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oDelivery->loadInLang(key($oOtherLang), $soxId);
            }

            $this->_aViewData["edit"] = $oDelivery;

            //Disable editing for derived articles
            if ($oDelivery->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            // remove already created languages
            $aLang = array_diff($oLang->getLanguageNames(), $oOtherLang);
            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }

            // set selected delivery type
            if (!$oDelivery->oxdelivery__oxdeltype->value) {
                $oDelivery->oxdelivery__oxdeltype = new \OxidEsales\Eshop\Core\Field("a"); // default
            }
            $aDelTypes[$oDelivery->oxdelivery__oxdeltype->value]->selected = true;
        }

        $this->_aViewData["deltypes"] = $aDelTypes;

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oDeliveryMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryMainAjax::class);
            $this->_aViewData['oxajax'] = $oDeliveryMainAjax->getColumns();

            return "popups/delivery_main.tpl";
        }

        return "delivery_main.tpl";
    }

    /**
     * Saves delivery information changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oDelivery = oxNew(\OxidEsales\Eshop\Application\Model\Delivery::class);

        if ($soxId != "-1") {
            $oDelivery->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxdelivery__oxid'] = null;
        }

        // checkbox handling
        if (!isset($aParams['oxdelivery__oxactive'])) {
            $aParams['oxdelivery__oxactive'] = 0;
        }

        if (!isset($aParams['oxdelivery__oxfixed'])) {
            $aParams['oxdelivery__oxfixed'] = 0;
        }

        if (!isset($aParams['oxdelivery__oxfinalize'])) {
            $aParams['oxdelivery__oxfinalize'] = 0;
        }

        if (!isset($aParams['oxdelivery__oxsort'])) {
            $aParams['oxdelivery__oxsort'] = 9999;
        }

        //Disable editing for derived articles
        if ($oDelivery->isDerived()) {
            return;
        }

        $oDelivery->setLanguage(0);
        $oDelivery->assign($aParams);
        $oDelivery->setLanguage($this->_iEditLang);
        $oDelivery = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oDelivery);
        $oDelivery->save();

        // set oxid if inserted
        $this->setEditObjectId($oDelivery->getId());
    }

    /**
     * Saves delivery information changes.
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oDelivery = oxNew(\OxidEsales\Eshop\Application\Model\Delivery::class);

        if ($soxId != "-1") {
            $oDelivery->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxdelivery__oxid'] = null;
        }

        // checkbox handling
        if (!isset($aParams['oxdelivery__oxactive'])) {
            $aParams['oxdelivery__oxactive'] = 0;
        }
        if (!isset($aParams['oxdelivery__oxfixed'])) {
            $aParams['oxdelivery__oxfixed'] = 0;
        }

        //Disable editing for derived articles
        if ($oDelivery->isDerived()) {
            return;
        }

        $oDelivery->setLanguage(0);
        $oDelivery->assign($aParams);
        $oDelivery->setLanguage($this->_iEditLang);
        $oDelivery = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oDelivery);
        $oDelivery->save();

        // set oxid if inserted
        $this->setEditObjectId($oDelivery->getId());
    }

    /**
     * returns delivery types
     *
     * @return array
     */
    public function getDeliveryTypes()
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $iLang = $oLang->getTplLanguage();

        $aDelTypes = [];
        $oType = new stdClass();
        $oType->sType = "a";      // amount
        $oType->sDesc = $oLang->translateString("amount", $iLang);
        $aDelTypes['a'] = $oType;
        $oType = new stdClass();
        $oType->sType = "s";      // Size
        $oType->sDesc = $oLang->translateString("size", $iLang);
        $aDelTypes['s'] = $oType;
        $oType = new stdClass();
        $oType->sType = "w";      // Weight
        $oType->sDesc = $oLang->translateString("weight", $iLang);
        $aDelTypes['w'] = $oType;
        $oType = new stdClass();
        $oType->sType = "p";      // Price
        $oType->sDesc = $oLang->translateString("price", $iLang);
        $aDelTypes['p'] = $oType;

        return $aDelTypes;
    }
}
