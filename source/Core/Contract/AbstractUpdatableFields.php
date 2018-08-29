<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * Abstraction for handling fields which could be modified by shop customer.
 */
abstract class AbstractUpdatableFields
{
    /** @var string */
    protected $tableName;

    /**
     * Return list of fields which could be updated by shop customer.
     */
    abstract public function getUpdatableFields();

    /**
     * Get table name of a model.
     * Table name could be used to form full name together with field.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}
