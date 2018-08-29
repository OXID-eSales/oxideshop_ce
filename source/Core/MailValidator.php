<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class MailValidator
 */
class MailValidator
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
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
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

        return (getStr()->preg_match($sEmailRule, $sEmail) != 0);
    }
}
