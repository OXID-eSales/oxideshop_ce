<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

use OxidEsales\Eshop\Core\Form\FormFields as EshopFormFields;

/**
 * Trimm FormFields.
 */
interface FormFieldsTrimmerInterface
{
    /**
     * Returns trimmed fields.
     */
    public function trim(EshopFormFields $formFields);
}
