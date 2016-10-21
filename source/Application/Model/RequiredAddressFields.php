<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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
    private $_aDefaultRequiredFields = array(
        'oxuser__oxfname',
        'oxuser__oxlname',
        'oxuser__oxstreetnr',
        'oxuser__oxstreet',
        'oxuser__oxzip',
        'oxuser__oxcity'
    );

    /**
     * Required fields.
     *
     * @var array
     */
    private $_aRequiredFields = array();

    /**
     * Sets default required fields either from config or from _aDefaultRequiredFields.
     *
     */
    public function __construct()
    {
        $this->setRequiredFields($this->_aDefaultRequiredFields);

        $aRequiredFields = oxRegistry::getConfig()->getConfigParam('aMustFillFields');
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
        $aAllowed = array();
        foreach ($aFields as $sKey => $sValue) {
            if (strpos($sValue, $sPrefix) === 0) {
                $aAllowed[] = $aFields[$sKey];
            }
        }

        return $aAllowed;
    }
}
