<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * Exception class for a non existing language local
 *
 * @deprecated since 5.2.8 (2016.02.05); Will be removed as not used in code.
 */
class LanguageException extends \OxidEsales\Eshop\Core\Exception\StandardException
{
    /**
     * Exception type, currently old class name is used.
     *
     * @var string
     */
    protected $type = 'oxLanguageException';

    /**
     * Language constant
     *
     * @var string
     */
    private $_sLangConstant = "";

    /**
     * sets the language constant which is missing
     *
     * @param string $sLangConstant language constant
     */
    public function setLangConstant($sLangConstant)
    {
        $this->_sLangConstant = $sLangConstant;
    }

    /**
     * Get language constant
     *
     * @return string
     */
    public function getLangConstant()
    {
        return $this->_sLangConstant;
    }

    /**
     * Get string dump
     * Overrides oxException::getString()
     *
     * @return string
     */
    public function getString()
    {
        return __CLASS__ . '-' . parent::getString() . " Faulty Constant --> " . $this->_sLangConstant . "\n";
    }

    /**
     * Creates an array of field name => field value of the object
     * to make a easy conversion of exceptions to error messages possible
     * Overrides oxException::getValues()
     * should be extended when additional fields are used!
     *
     * @return array
     */
    public function getValues()
    {
        $aRes = parent::getValues();
        $aRes['langConstant'] = $this->getLangConstant();

        return $aRes;
    }
}
