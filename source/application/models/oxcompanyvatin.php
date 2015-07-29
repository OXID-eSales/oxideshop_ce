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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */


/**
 * Company VAT identification number (VATIN)
 */
class oxCompanyVatIn
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
        return (string) oxStr::getStr()->strtoupper(oxStr::getStr()->substr($this->_cleanUp($this->_sCompanyVatNumber), 0, 2));
    }

    /**
     * Returns country code from number.
     *
     * @return string
     */
    public function getNumbers()
    {
        return (string) oxStr::getStr()->substr($this->_cleanUp($this->_sCompanyVatNumber), 2);
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
        return (string) oxStr::getStr()->preg_replace("/\s|-/", '', $sValue);
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
