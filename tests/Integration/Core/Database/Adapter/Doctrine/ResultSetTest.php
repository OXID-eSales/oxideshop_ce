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

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Database\Adapter\Doctrine;

use OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database;

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
     * @var string The name of the class, including the complete namespace.
     */
    const CLASS_NAME_WITH_PATH = 'OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\ResultSet';

    /**
     * @var string The database exception class to be thrown
     */
    const DATABASE_EXCEPTION_CLASS = 'OxidEsales\EshopCommunity\Core\Exception\DatabaseException';

    /**
     * @var string The result set class class
     */
    const RESULT_SET_CLASS = 'OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\ResultSet';

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
