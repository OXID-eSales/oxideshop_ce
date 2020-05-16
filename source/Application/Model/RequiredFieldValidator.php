<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Class for validating address
 */
class RequiredFieldValidator
{
    /**
     * Validates field value.
     *
     * @param string $sFieldValue Field value
     *
     * @return bool
     */
    public function validateFieldValue($sFieldValue)
    {
        $blValid = true;
        if (is_array($sFieldValue)) {
            $blValid = $this->_validateFieldValueArray($sFieldValue);
        } else {
            if (!trim($sFieldValue)) {
                $blValid = false;
            }
        }

        return $blValid;
    }

    /**
     * Checks if all values are filled up
     *
     * @param array $aFieldValues field values
     *
     * @return bool
     * @deprecated underscore prefix violates PSR12, will be renamed to "validateFieldValueArray" in next major
     */
    private function _validateFieldValueArray($aFieldValues) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $blValid = true;
        foreach ($aFieldValues as $sValue) {
            if (!trim($sValue)) {
                $blValid = false;
                break;
            }
        }

        return $blValid;
    }
}
