<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Application\Model\CompanyVatIn;
use OxidEsales\Eshop\Application\Model\Country;

/**
 * Company VAT identification number checker. Check if number belongs to the country.
 */
class CompanyVatInCountryChecker extends \OxidEsales\Eshop\Core\CompanyVatInChecker implements \OxidEsales\Eshop\Core\Contract\ICountryAware
{
    /**
     * Error string if country mismatch
     */
    const ERROR_ID_NOT_VALID = 'ID_NOT_VALID';

    /**
     * Country
     *
     * @var Country
     */
    private $_oCountry = null;

    /**
     * Country setter
     */
    public function setCountry(Country $country)
    {
        $this->_oCountry = $country;
    }

    /**
     * Country getter
     *
     * @return Country
     */
    public function getCountry()
    {
        return $this->_oCountry;
    }

    public function validate(CompanyVatIn $vatIn)
    {
        $country = $this->getCountry();

        if (is_null($country)) {
            return false;
        }

        if (!$this->hasValidVatPrefix($country)) {
            $this->setError('MISSING_COUNTRY_PREFIX');
            return false;
        }

        return $this->isVatPrefixMatching($country, $vatIn);
    }

    private function hasValidVatPrefix(Country $country): bool
    {
        return !empty($country->getVATIdentificationNumberPrefix());
    }

    private function isVatPrefixMatching(Country $country, CompanyVatIn $companyVatIn): bool
    {
        $prefix = $country->getVATIdentificationNumberPrefix();
        $isValid = ($prefix === $companyVatIn->getCountryCode());

        if (!$isValid) {
            $this->setError(self::ERROR_ID_NOT_VALID);
        }

        return $isValid;
    }
}
