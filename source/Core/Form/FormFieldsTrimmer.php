<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

use OxidEsales\Eshop\Core\Form\FormFieldsTrimmerInterface as EshopFormFieldsTrimmerInterface;

/**
 * Trimm FormFields.
 */
class FormFieldsTrimmer implements EshopFormFieldsTrimmerInterface
{
    /**
     * Returns trimmed fields.
     *
     * @param  array $fields
     *
     * @return mixed
     */
    public function trim($fields)
    {
        foreach ($fields as $index => $value) {
            if ($this->isTrimmableField($value)) {
                $fields[$index] = $this->trimField($value);
            }

            if ($this->isSetOfFields($value)) {
                $fields[$index] = $this->trim($value);
            }
        }

        return $fields;
    }

    /**
     * @param  mixed $value
     *
     * @return bool
     */
    private function isTrimmableField($value)
    {
        return is_string($value);
    }

    /**
     * @param   mixed $value
     *
     * @return  bool
     */
    private function isSetOfFields($value)
    {
        return is_array($value);
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
