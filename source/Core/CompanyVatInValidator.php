<?php

declare(strict_types=1);

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
     * Array of validators (checkers).
     *
     * @var array
     */
    private $_aCheckers = [];

    /**
     * Error message.
     *
     * @var string
     */
    private $_sError = '';

    /**
     * Country setter.
     */
    public function setCountry(\OxidEsales\Eshop\Application\Model\Country $country): void
    {
        $this->_oCountry = $country;
    }

    /**
     * Country getter.
     *
     * @return \OxidEsales\Eshop\Application\Model\Country
     */
    public function getCountry()
    {
        return $this->_oCountry;
    }

    /**
     * Error setter.
     *
     * @param string $error
     */
    public function setError($error): void
    {
        $this->_sError = $error;
    }

    /**
     * Error getter.
     *
     * @return string
     */
    public function getError()
    {
        return $this->_sError;
    }

    /**
     * Constructor.
     */
    public function __construct(\OxidEsales\Eshop\Application\Model\Country $country)
    {
        $this->setCountry($country);
    }

    /**
     * Adds validator.
     */
    public function addChecker(\OxidEsales\Eshop\Core\CompanyVatInChecker $validator): void
    {
        $this->_aCheckers[] = $validator;
    }

    /**
     * Returns added validators.
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
