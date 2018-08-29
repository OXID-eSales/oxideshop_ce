<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

use OxidEsales\Eshop\Core\Form\FormFieldsTrimmerInterface as EshopFormFieldsTrimmerInterface;
use OxidEsales\Eshop\Core\Form\FormFields as EshopFormFields;

/**
 * Trim FormFields.
 */
class FormFieldsTrimmer implements EshopFormFieldsTrimmerInterface
{
    /**
     * Returns trimmed fields.
     *
     * @param EshopFormFields $fields to trim.
     *
     * @return array
     */
    public function trim(EshopFormFields $fields)
    {
        $updatableFields = $fields->getUpdatableFields();

        array_walk_recursive($updatableFields, function (&$value) {
            $value = $this->isTrimmableField($value) ? $this->trimField($value) : $value;
        });

        return $updatableFields;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isTrimmableField($value)
    {
        return is_string($value);
    }

    /**
     * Returns trimmed field value.
     *
     * @param   string $field
     *
     * @return  string
     */
    private function trimField($field)
    {
        return trim($field);
    }
}
