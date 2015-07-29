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
 * Company VAT identification number validator. Executes added validators on given VATIN.
 */
class oxCompanyVatInValidator
{

    /**
     * @var oxCountry
     */
    private $_oCountry = null;

    /**
     * Array of validators (checkers)
     *
     * @var array
     */
    private $_aCheckers = array();

    /**
     * Error message
     *
     * @var string
     */
    private $_sError = '';

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
     * Error setter
     *
     * @param string $sError
     */
    public function setError($sError)
    {
        $this->_sError = $sError;
    }

    /**
     * Error getter
     *
     * @return string
     */
    public function getError()
    {
        return $this->_sError;
    }

    /**
     * Constructor
     *
     * @param oxCountry $oCountry
     */
    public function __construct(oxCountry $oCountry)
    {
        $this->setCountry($oCountry);
    }

    /**
     * Adds validator
     *
     * @param oxCompanyVatInChecker $oValidator
     */
    public function addChecker(oxCompanyVatInChecker $oValidator)
    {
        $this->_aCheckers[] = $oValidator;
    }

    /**
     * Returns added validators
     *
     * @return array
     */
    public function getCheckers()
    {
        return $this->_aCheckers;
    }

    /**
     * Validate company VAT identification number.
     *
     * @param oxCompanyVatIn $oCompanyVatNumber
     *
     * @return bool
     */
    public function validate(oxCompanyVatIn $oCompanyVatNumber)
    {
        $blResult = false;
        $aValidators = $this->getCheckers();

        foreach ($aValidators as $oValidator) {
            $blResult = true;
            if ($oValidator instanceof oxICountryAware) {
                $oValidator->setCountry($this->getCountry());
            }

            if (!$oValidator->validate($oCompanyVatNumber)) {
                $blResult = false;
                $this->setError($oValidator->getError());
                break;
            }
        }

        return $blResult;
    }
}
