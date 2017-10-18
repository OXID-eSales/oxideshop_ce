<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Admin order address manager.
 * Collects order addressing information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders -> Address.
 */
class OrderAddress extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxorder object
     * and passes it's data to Smarty engine. Returns name of template
     * file "order_address.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            $oOrder->load($soxId);

            $this->_aViewData["edit"] = $oOrder;
        }

        $oCountryList = oxNew(\OxidEsales\Eshop\Application\Model\CountryList::class);
        $oCountryList->loadActiveCountries(\OxidEsales\Eshop\Core\Registry::getLang()->getObjectTplLanguage());

        $this->_aViewData["countrylist"] = $oCountryList;

        return "order_address.tpl";
    }

    /**
     * Iterates through data array, checks if specified fields are filled
     * in, cleanups not needed data
     *
     * @param array  $aData          data to process
     * @param string $sTypeToProcess data type to process e.g. "oxorder__oxdel"
     * @param array  $aIgnore        fields which must be ignored while processing
     *
     * @return null
     */
    protected function _processAddress($aData, $sTypeToProcess, $aIgnore)
    {
        // empty address fields?
        $blEmpty = true;

        // here we will store names of fields which needs to be cleaned up
        $aFields = [];

        foreach ($aData as $sName => $sValue) {
            // if field type matches..
            if (strpos($sName, $sTypeToProcess) !== false) {
                // storing which fields must be unset..
                $aFields[] = $sName;

                // ignoring whats need to be ignored and testing values
                if (!in_array($sName, $aIgnore) && $sValue) {
                    // something was found - means leaving as is..
                    $blEmpty = false;
                    break;
                }
            }
        }

        // cleanup if empty
        if ($blEmpty) {
            foreach ($aFields as $sName) {
                $aData[$sName] = "";
            }
        }

        return $aData;
    }

    /**
     * Saves ordering address information.
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = (array) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if ($soxId != "-1") {
            $oOrder->load($soxId);
        } else {
            $aParams['oxorder__oxid'] = null;
        }

        $aParams = $this->_processAddress($aParams, "oxorder__oxdel", ["oxorder__oxdelsal"]);
        $oOrder->assign($aParams);
        $oOrder->save();

        // set oxid if inserted
        $this->setEditObjectId($oOrder->getId());
    }
}
