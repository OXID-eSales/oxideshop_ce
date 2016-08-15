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

namespace OxidEsales\Eshop\Tests\Integration\Core\Database;

use OxidEsales\Eshop\Core\Database\DatabaseInterface;
use OxidEsales\Eshop\Core\LegacyDatabase;

/**
 * Tests for our database object.
 *
 * @group database-adapter
 */
class LegacyDatabaseTest extends DatabaseInterfaceImplementationTest
{
    /**
     * @var string The database exception class to be thrown
     */
    const DATABASE_EXCEPTION_CLASS = 'oxAdoDbException';

    /**
     * @var string The result set class class
     */
    const RESULT_SET_CLASS = 'ADORecordSet';

    /**
     * @var string The empty result set class class
     */
    const EMPTY_RESULT_SET_CLASS = 'ADORecordSet_empty';

    /**
     * @var bool Use the legacy database adapter.
     *
     * @todo get rid of this
     */
    const USE_LEGACY_DATABASE = true;

    /**
     * @var DatabaseInterface|LegacyDatabase The database to test.
     */
    protected $database = null;

    /**
     * @return string The name of the database exception class
     */
    protected function getDatabaseExceptionClassName()
    {
        return self::DATABASE_EXCEPTION_CLASS;
    }

    /**
     * @return string The name of the result set class
     */
    protected function getResultSetClassName()
    {
        return self::RESULT_SET_CLASS;
    }

    /**
     * @return string The name of the empty result set class
     */
    protected function getEmptyResultSetClassName()
    {
        return self::EMPTY_RESULT_SET_CLASS;
    }

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
     * Close the database connection.
     * As in this case the database handle is a singleton, closing the connection at each tearDown is not useful.
     */
    protected function closeConnection()
    {
    }
}
