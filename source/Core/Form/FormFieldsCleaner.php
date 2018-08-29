<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

/**
 * Return those fields which could be changed by a customer.
 */
class FormFieldsCleaner
{
    /** @var FormFields */
    private $updatableFields;

    /**
     * @param FormFields $updatableFields White-list.
     */
    public function __construct(\OxidEsales\Eshop\Core\Form\FormFields $updatableFields)
    {
        $this->updatableFields = $updatableFields;
    }

    /**
     * Return only those items which exist in both lists.
     *
     * @param array $listToClean All fields.
     *
     * @return array
     */
    public function filterByUpdatableFields(array $listToClean)
    {
        $allowedFields = $this->updatableFields->getUpdatableFields();

        $cleanedList = $listToClean;
        if ($allowedFields->count() > 0) {
            $cleanedList = $this->filterFieldsByWhiteList($allowedFields, $listToClean);
        }

        return $cleanedList;
    }

    /**
     * Return fields by performing a case-insensitive compare.
     * Does not change original case-sensitivity of fields.
     *
     * @param \ArrayIterator $allowedFields
     * @param array          $listToClean
     *
     * @return array
     */
    private function filterFieldsByWhiteList(\ArrayIterator $allowedFields, array $listToClean)
    {
        $allowedFieldsLowerCase = array_map('strtolower', (array)$allowedFields);
        $cleanedList = array_filter($listToClean, function ($field) use ($allowedFieldsLowerCase) {
            return in_array(strtolower($field), $allowedFieldsLowerCase);
        }, ARRAY_FILTER_USE_KEY);

        return $cleanedList;
    }
}
