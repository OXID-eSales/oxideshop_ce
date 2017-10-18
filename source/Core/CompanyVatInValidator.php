<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Company VAT identification number validator. Executes added validators on given VATIN.
 */
class CompanyVatInValidator
{
    /**
     * @var \OxidEsales\Eshop\Application\Model\Country
     */
    private $_oCountry = null;

    /**
     * Array of validators (checkers)
     *
     * @var array
     */
    private $_aCheckers = [];

    /**
     * Error message
     *
     * @var string
     */
    private $_sError = '';

    /**
     * Country setter
     *
     * @param \OxidEsales\Eshop\Application\Model\Country $country
     */
    public function setCountry(\OxidEsales\Eshop\Application\Model\Country $country)
    {
        $this->_oCountry = $country;
    }

    /**
     * Country getter
     *
     * @return \OxidEsales\Eshop\Application\Model\Country
     */
    public function getCountry()
    {
        return $this->_oCountry;
    }

    /**
     * Error setter
     *
     * @param string $error
     */
    public function setError($error)
    {
        $this->_sError = $error;
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
     * @param \OxidEsales\Eshop\Application\Model\Country $country
     */
    public function __construct(\OxidEsales\Eshop\Application\Model\Country $country)
    {
        $this->setCountry($country);
    }

    /**
     * Adds validator
     *
     * @param \OxidEsales\Eshop\Core\CompanyVatInChecker $validator
     */
    public function addChecker(\OxidEsales\Eshop\Core\CompanyVatInChecker $validator)
    {
        $this->_aCheckers[] = $validator;
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
     * @param \OxidEsales\Eshop\Application\Model\CompanyVatIn $companyVatNumber
     *
     * @return bool
     */
    public function validate(\OxidEsales\Eshop\Application\Model\CompanyVatIn $companyVatNumber)
    {
        $result = false;
        $validators = $this->getCheckers();

        foreach ($validators as $validator) {
            $result = true;
            if ($validator instanceof \OxidEsales\Eshop\Core\Contract\ICountryAware) {
                $validator->setCountry($this->getCountry());
            }

            if (!$validator->validate($companyVatNumber)) {
                $result = false;
                $this->setError($validator->getError());
                break;
            }
        }

        return $result;
    }
}
