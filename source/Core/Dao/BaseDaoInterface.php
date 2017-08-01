<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\Dao;

/**
 * Data access object interface.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
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
