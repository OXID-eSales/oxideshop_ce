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
 * Company VAT identification number checker. Check if number belongs to the country.
 */
class oxCompanyVatInCountryChecker extends oxCompanyVatInChecker implements oxICountryAware
{

    /**
     * Error string if country mismatch
     */
    const ERROR_ID_NOT_VALID = 'ID_NOT_VALID';

    /**
     * Country
     *
     * @var oxCountry
     */
    private $_oCountry = null;

    /**
     * Country setter
     *
     * @param oxCountry $oCountry
     */
    public function setCountry(oxCountry $oCountry)
    {
        $this->_oCountry = $oCountry;
    }

    /**
     * Country getter
     *
     * @return oxCountry
     */
    public function getCountry()
    {
        return $this->_oCountry;
    }

    /**
     * Validates.
     *
     * @param oxCompanyVatIn $oVatIn
     *
     * @return bool
     */
    public function validate(oxCompanyVatIn $oVatIn)
    {
        $blResult = false;
        $oCountry = $this->getCountry();
        if (!is_null($oCountry)) {
            $blResult = ($oCountry->getVATIdentificationNumberPrefix() === $oVatIn->getCountryCode());
            if (!$blResult) {
                $this->setError(self::ERROR_ID_NOT_VALID);
            }
        }

        return $blResult;
    }
}
