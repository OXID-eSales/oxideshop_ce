<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxStr;

/**
 * Company VAT identification number (VATIN)
 */
class CompanyVatIn
{
    /**
     * VAT identification number
     *
     * @var string
     */
    private $_sCompanyVatNumber;

    /**
     * Constructor
     *
     * @param string $sCompanyVatNumber - company vat identification number.
     */
    public function __construct($sCompanyVatNumber)
    {
        $this->_sCompanyVatNumber = $sCompanyVatNumber;
    }

    /**
     * Returns country code from number.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return (string) \OxidEsales\Eshop\Core\Str::getStr()->strtoupper(\OxidEsales\Eshop\Core\Str::getStr()->substr($this->_cleanUp($this->_sCompanyVatNumber), 0, 2));
    }

    /**
     * Returns country code from number.
     *
     * @return string
     */
    public function getNumbers()
    {
        return (string) \OxidEsales\Eshop\Core\Str::getStr()->substr($this->_cleanUp($this->_sCompanyVatNumber), 2);
    }

    /**
     * Removes spaces and symbols: '-',',','.' from string
     *
     * @param string $sValue Value.
     *
     * @return string
     */
    protected function _cleanUp($sValue)
    {
        return (string) \OxidEsales\Eshop\Core\Str::getStr()->preg_replace("/\s|-/", '', $sValue);
    }


    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_sCompanyVatNumber;
    }
}
