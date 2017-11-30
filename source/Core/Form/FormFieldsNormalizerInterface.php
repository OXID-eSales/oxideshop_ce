<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

use OxidEsales\Eshop\Core\Form\FormFields as EshopFormFields;

/**
 * Normalize FormFields.
 */
interface FormFieldsNormalizerInterface
{
    /**
     * Returns normalized fields.
     *
     * @param  FormFields $formFields
     *
     * @return ArrayIterator
     */
    public function normalize(EshopFormFields $formFields);
}
