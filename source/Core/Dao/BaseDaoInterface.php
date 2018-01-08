<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Dao;

/**
 * Data access object interface.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
interface BaseDaoInterface
{
    /**
     * Finds all entities.
     *
     * @return array
     */
    public function findAll();

    /**
     * Deletes the entity with the given id.
     *
     * @param string $id An id of the entity to delete.
     */
    public function delete($id);

    /**
     * Updates or insert the given entity.
     *
     * @param object $object
     */
    public function save($object);

    /**
     * Start a database transaction.
     */
    public function startTransaction();

    /**
     * Commit a database transaction.
     */
    public function commitTransaction();

    /**
     * RollBack a database transaction.
     */
    public function rollbackTransaction();
}
