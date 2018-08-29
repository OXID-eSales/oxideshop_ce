<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * exceptions for missing components e.g.:
 * - missing class
 * - missing function
 * - missing template
 * - missing field in object
 */
class SystemComponentException extends \OxidEsales\Eshop\Core\Exception\StandardException
{
    /**
     * Exception type, currently old class name is used.
     *
     * @var string
     */
    protected $type = 'oxSystemComponentException';

    /**
     * Component causing the exception.
     *
     * @var string
     */
    private $_sComponent;

    /**
     * Sets the component name which caused the exception as a string.
     *
     * @param string $sComponent name of component
     */
    public function setComponent($sComponent)
    {
        $this->_sComponent = $sComponent;
    }

    /**
     * Name of the component that caused the exception
     *
     * @return string
     */
    public function getComponent()
    {
        return $this->_sComponent;
    }

    /**
     * Get string dump
     * Overrides oxException::getString()
     *
     * @return string
     */
    public function getString()
    {
        return __CLASS__ . '-' . parent::getString() . " Faulty component --> " . $this->_sComponent;
    }

    /**
     * Creates an array of field name => field value of the object.
     * To make a easy conversion of exceptions to error messages possible.
     * Should be extended when additional fields are used!
     * Overrides oxException::getValues().
     *
     * @return array
     */
    public function getValues()
    {
        $aRes = parent::getValues();
        $aRes['component'] = $this->getComponent();

        return $aRes;
    }
}
