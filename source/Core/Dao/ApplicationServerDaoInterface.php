<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Dao;

/**
 * Application server data access manager.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
interface ApplicationServerDaoInterface extends \OxidEsales\Eshop\Core\Dao\BaseDaoInterface
{
    /**
     * Finds an application server by given id, null if none is found.
     *
     * @param string $id An id of the entity to find.
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer|null
     */
    public function findAppServer($id);
}
