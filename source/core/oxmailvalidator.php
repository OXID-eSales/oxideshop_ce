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
 * Class oxMailValidator
 */
class oxMailValidator
{

    /**
     * @var string
     */
    private $_sMailValidationRule = null;

    /**
     * Get mail validation rule.
     *
     * @return string
     */
    public function getMailValidationRule()
    {
        if (is_null($this->_sMailValidationRule)) {
            $this->_sMailValidationRule = "/^([\w+\-.])+\@([\w\-.])+\.([A-Za-z]{2,64})$/i";
        }

        return $this->_sMailValidationRule;
    }

    /**
     * Override mail validation rule.
     *
     * @param string $sMailValidationRule mail validation rule
     */
    public function setMailValidationRule($sMailValidationRule)
    {
        $this->_sMailValidationRule = $sMailValidationRule;
    }

    /**
     * Set mail validation rule from config.
     * Would use default rule if not defined in config.
     */
    public function __construct()
    {
        $oConfig = oxRegistry::getConfig();
        $sEmailValidationRule = $oConfig->getConfigParam('sEmailValidationRule');
        if (!empty($sEmailValidationRule)) {
            $this->_sMailValidationRule = $sEmailValidationRule;
        }
    }

    /**
     * User email validation function. Returns true if email is OK otherwise - false;
     * Syntax validation is performed only.
     *
     * @param string $sEmail user email
     *
     * @return bool
     */
    public function isValidEmail($sEmail)
    {
        $sEmailRule = $this->getMailValidationRule();
        $blValid = (getStr()->preg_match($sEmailRule, $sEmail) != 0);

        return $blValid;
    }
}
