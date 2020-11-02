<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Company VAT identification number (VATIN) checker.
 */
abstract class CompanyVatInChecker
{
    /**
     * Error message.
     *
     * @var string
     */
    protected $_sError = '';

    /**
     * Error message setter.
     *
     * @param string $error
     */
    public function setError($error): void
    {
        $this->_sError = $error;
    }

    /**
     * Error message getter.
     *
     * @return string
     */
    public function getError()
    {
        return $this->_sError;
    }

    /**
     * Validates company VAT identification number.
     *
     * @return mixed
     */
    abstract public function validate(\OxidEsales\Eshop\Application\Model\CompanyVatIn $vatIn);
}
