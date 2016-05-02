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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

namespace OxidEsales\Eshop\Tests\integration\core\Database\Adapter;

/**
 * Class LegacyResultSetTest
 *
 * @package OxidEsales\Eshop\Tests\integration\core\Database\Adapter
 *
 * @group database-adapter
 */
class LegacyResultSetTest extends ResultSetTest
{

    /**
     * @var string The result set class class
     */
    const RESULT_SET_CLASS = 'object_ResultSet';

    /**
     * @var string The empty result set class class
     */
    const EMPTY_RESULT_SET_CLASS = 'OxidEsales\Eshop\Core\Database\DoctrineEmptyResultSet';

    /**
     * Create the database object under test.
     *
     * @return \oxLegacyDb The database object under test.
     */
    protected function createDatabase()
    {
        return \oxDb::getDb();
    }

    /**
     * Create the database object under test - the static pendant to use in the setUpBeforeClass and tearDownAfterClass.
     *
     * @return \oxLegacyDb The database object under test.
     */
    protected static function createDatabaseStatic()
    {
        return \oxDb::getDb();
    }
}
