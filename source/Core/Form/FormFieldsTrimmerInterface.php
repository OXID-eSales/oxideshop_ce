<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

/**
 * Trimm FormFields.
 */
interface FormFieldsTrimmerInterface
{
    /**
     * Returns trimmed fields.
     *
     * @param  array $fields
     */
    public function trim($fields);
}
