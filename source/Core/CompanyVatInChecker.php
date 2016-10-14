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

namespace OxidEsales\Eshop\Core;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use oxCompanyVatIn;

/**
 * Company VAT identification number (VATIN) checker
 *
 */
abstract class CompanyVatInChecker implements LoggerAwareInterface
{
     use LoggerAwareTrait;

    /**
     * Error message
     *
     * @var string
     */
    protected $_sError = '';

    /**
     * Error message setter
     *
     * @param string $error
     */
    public function setError($error)
    {
        $this->_sError = $error;
    }

    /**
     * Error message getter
     *
     * @return string
     */
    public function getError()
    {
        return $this->_sError;
    }

    /**
     * Validates company VAT identification number
     *
     * @param oxCompanyVatIn $vatIn
     *
     * @return mixed
     */
    abstract public function validate(oxCompanyVatIn $vatIn);
}
