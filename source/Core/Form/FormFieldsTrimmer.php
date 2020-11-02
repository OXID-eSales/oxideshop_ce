<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

use OxidEsales\Eshop\Core\Form\FormFields as EshopFormFields;
use OxidEsales\Eshop\Core\Form\FormFieldsTrimmerInterface as EshopFormFieldsTrimmerInterface;

/**
 * Trim FormFields.
 */
class FormFieldsTrimmer implements EshopFormFieldsTrimmerInterface
{
    /**
     * Returns trimmed fields.
     *
     * @param EshopFormFields $fields to trim
     *
     * @return \ArrayIterator
     */
    public function trim(EshopFormFields $fields)
    {
        $updatableFields = $fields->getUpdatableFields()->getArrayCopy();

        array_walk_recursive($updatableFields, function (&$value): void {
            $value = $this->isTrimmableField($value) ? $this->trimField($value) : $value;
        });

        return new \ArrayIterator($updatableFields);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isTrimmableField($value)
    {
        return \is_string($value);
    }

    /**
     * Returns trimmed field value.
     *
     * @param string $field
     *
     * @return string
     */
    private function trimField($field)
    {
        return trim($field);
    }
}
