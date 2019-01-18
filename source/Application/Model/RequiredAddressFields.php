<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;

/**
 * Defines and returns delivery and billing required fields.
 */
class RequiredAddressFields
{
    /**
     * Default required fields for use when not set in config.
     *
     * @var array
     */
    private $_aDefaultRequiredFields = [
        'oxuser__oxfname',
        'oxuser__oxlname',
        'oxuser__oxstreetnr',
        'oxuser__oxstreet',
        'oxuser__oxzip',
        'oxuser__oxcity'
    ];

    /**
     * Required fields.
     *
     * @var array
     */
    private $_aRequiredFields = [];

    /**
     * Sets default required fields either from config or from _aDefaultRequiredFields.
     *
     */
    public function __construct()
    {
        $this->setRequiredFields($this->_aDefaultRequiredFields);

        $aRequiredFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aMustFillFields');
        if (is_array($aRequiredFields)) {
            $this->setRequiredFields($aRequiredFields);
        }
    }

    /**
     * Sets all required fields.
     *
     * @param array $aRequiredFields
     */
    public function setRequiredFields($aRequiredFields)
    {
        $this->_aRequiredFields = $aRequiredFields;
    }

    /**
     * Returns all required fields.
     *
     * @return array
     */
    public function getRequiredFields()
    {
        return $this->_aRequiredFields;
    }

    /**
     * Returns required fields for user address validation.
     *
     * @return mixed
     */
    public function getBillingFields()
    {
        $aRequiredFields = $this->getRequiredFields();

        return $this->_filterFields($aRequiredFields, 'oxuser__');
    }

    /**
     * Returns required fields for delivery address validation.
     *
     * @return mixed
     */
    public function getDeliveryFields()
    {
        $aRequiredFields = $this->getRequiredFields();

        return $this->_filterFields($aRequiredFields, 'oxaddress__');
    }

    /**
     * Removes delivery fields from fields list.
     *
     * @param array  $aFields
     * @param string $sPrefix
     *
     * @return mixed
     */
    private function _filterFields($aFields, $sPrefix)
    {
        $aAllowed = [];
        foreach ($aFields as $sKey => $sValue) {
            if (strpos($sValue, $sPrefix) === 0) {
                $aAllowed[] = $aFields[$sKey];
            }
        }

        return $aAllowed;
    }
}
