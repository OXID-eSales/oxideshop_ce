<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

use OxidEsales\Eshop\Core\Form\FormFieldsTrimmerInterface as EshopFormFieldsTrimmerInterface;
use OxidEsales\Eshop\Core\Form\FormFieldsNormalizerInterface as EshopFormFieldsNormalizerInterface;

use OxidEsales\Eshop\Core\Form\FormFields as EshopFormFields;

/**
 * Normalize FormFields.
 */
class FormFieldsNormalizer implements EshopFormFieldsNormalizerInterface
{
    /**
     * @var FormFieldsTrimmerInterface
     */
    private $formFieldsTrimmer;

    /**
     * @param FormFieldsTrimmerInterface $formFieldsTrimmer
     */
    public function __construct(EshopFormFieldsTrimmerInterface $formFieldsTrimmer)
    {
        $this->formFieldsTrimmer = $formFieldsTrimmer;
    }

    /**
     * Returns normalized fields.
     *
     * @param  FormFields $formFields
     *
     * @return ArrayIterator
     */
    public function normalize(EshopFormFields $formFields)
    {
        $fields             = $formFields->getUpdatableFields();
        $normalizedFields   = $this->formFieldsTrimmer->trim($fields);

        return $normalizedFields;
    }
}
