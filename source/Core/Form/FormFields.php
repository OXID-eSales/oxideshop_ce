<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Form;

/**
 * Unified way to define fields that are used in form fields.
 */
class FormFields
{
    /** @var array */
    private $updatableFields;

    /**
     * @param array $updatableFields
     */
    public function __construct(array $updatableFields)
    {
        $this->updatableFields = $updatableFields;
    }

    /**
     * @return \ArrayIterator
     */
    public function getUpdatableFields()
    {
        return new \ArrayIterator($this->updatableFields);
    }
}
