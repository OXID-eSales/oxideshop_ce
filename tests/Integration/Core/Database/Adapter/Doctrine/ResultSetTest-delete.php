<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Database\Adapter\Doctrine;

/**
 * Class DoctrineResultSetTest
 *
 * @package OxidEsales\EshopCommunity\Tests\integration\Core\Database\Adapter|Doctrine
 *
 * @group database-adapter
 */
class ResultSetTest extends ResultSetBaseTest
{
    /**
     * Create the database object under test.
     *
     * @return Doctrine The database object under test.
     */
    protected function createDatabase()
    {
        return \oxDb::getDb();
    }
}
