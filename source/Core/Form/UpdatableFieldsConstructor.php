<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

use OxidEsales\Eshop\Core\Model\FieldNameHelper;
use OxidEsales\Eshop\Core\Contract\AbstractUpdatableFields;

/**
 * Provides creators for cleaners of fields which could be updated by a customer.
 */
class UpdatableFieldsConstructor
{
    /**
     * Get cleaner for field list which are allowed to be submitted in a form.
     *
     * @param AbstractUpdatableFields $updatableFields
     *
     * @return FormFieldsCleaner
     */
    public function getAllowedFieldsCleaner(AbstractUpdatableFields $updatableFields)
    {
        $helper = oxNew(FieldNameHelper::class);
        $allowedFields = $helper->getFullFieldNames($updatableFields->getTableName(), $updatableFields->getUpdatableFields());

        $updatableFields = oxNew(\OxidEsales\Eshop\Core\Form\FormFields::class, $allowedFields);

        return oxNew(\OxidEsales\Eshop\Core\Form\FormFieldsCleaner::class, $updatableFields);
    }
}
