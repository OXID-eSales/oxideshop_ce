<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Class for validating address.
 */
class RequiredFieldsValidator
{
    /**
     * Required fields array.
     *
     * @var array
     */
    private $_aRequiredFields = [];

    /**
     * Invalid fields array.
     *
     * @var array
     */
    private $_aInvalidFields = [];

    /**
     * Required Field validator.
     *
     * @var \OxidEsales\Eshop\Application\Model\RequiredFieldValidator
     */
    private $_oFieldValidator = [];

    /**
     * Sets dependencies.
     *
     * @param \OxidEsales\Eshop\Application\Model\RequiredFieldValidator $oFieldValidator
     */
    public function __construct($oFieldValidator = null)
    {
        if (null === $oFieldValidator) {
            $oFieldValidator = oxNew(\OxidEsales\Eshop\Application\Model\RequiredFieldValidator::class);
        }
        $this->setFieldValidator($oFieldValidator);
    }

    /**
     * Returns required fields for address.
     *
     * @return array
     */
    public function getRequiredFields()
    {
        return $this->_aRequiredFields;
    }

    /**
     * Sets required fields array.
     *
     * @param array $aFields Fields
     */
    public function setRequiredFields($aFields): void
    {
        $this->_aRequiredFields = $aFields;
    }

    /**
     * Returns required fields for address.
     *
     * @return \OxidEsales\Eshop\Application\Model\RequiredFieldValidator
     */
    public function getFieldValidator()
    {
        return $this->_oFieldValidator;
    }

    /**
     * Sets required fields array.
     *
     * @param \OxidEsales\Eshop\Application\Model\RequiredFieldValidator $oFieldValidator
     */
    public function setFieldValidator($oFieldValidator): void
    {
        $this->_oFieldValidator = $oFieldValidator;
    }

    /**
     * Gets invalid fields.
     *
     * @return array
     */
    public function getInvalidFields()
    {
        return $this->_aInvalidFields;
    }

    /**
     * Checks if all required fields are filled.
     * Returns array of invalid fields or empty array if all fields are fine.
     *
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $oObject address fields with values
     *
     * @return bool if any invalid field exist
     */
    public function validateFields($oObject)
    {
        $aRequiredFields = $this->getRequiredFields();
        $oFieldValidator = $this->getFieldValidator();

        $aInvalidFields = [];
        foreach ($aRequiredFields as $sFieldName) {
            if (!$oFieldValidator->validateFieldValue($oObject->getFieldData($sFieldName))) {
                $aInvalidFields[] = $sFieldName;
            }
        }
        $this->_setInvalidFields($aInvalidFields);

        return empty($aInvalidFields);
    }

    /**
     * Add fields to invalid fields array.
     *
     * @param array $aFields invalid field name
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "setInvalidFields" in next major
     */
    private function _setInvalidFields($aFields): void // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $this->_aInvalidFields = $aFields;
    }
}
