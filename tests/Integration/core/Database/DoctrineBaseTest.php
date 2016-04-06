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

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Core\Database\Doctrine;

/**
 * Base class for database integration tests.
 *
 * @group doctrine
 */
abstract class Integration_Core_Database_DoctrineBaseTest extends UnitTestCase
{

    /**
     * @var string The name of the table, we use to test the database.
     */
    const TABLE_NAME = 'oxdoctrinetest';

    /**
     * @var string The first fixture oxId.
     */
    const FIXTURE_OXID_1 = 'OXID_1';

    /**
     * @var string The second fixture oxId.
     */
    const FIXTURE_OXID_2 = 'OXID_2';

    /**
     * @var string The third fixture oxId.
     */
    const FIXTURE_OXID_3 = 'OXID_3';

    /**
     * @var string The first fixture oxUserId.
     */
    const FIXTURE_OXUSERID_1 = 'OXUSERID_1';

    /**
     * @var string The first fixture oxUserId.
     */
    const FIXTURE_OXUSERID_2 = 'OXUSERID_2';

    /**
     * @var string The first fixture oxUserId.
     */
    const FIXTURE_OXUSERID_3 = 'OXUSERID_3';

    /**
     * @var Doctrine|oxLegacyDb The database to test.
     */
    protected $database = null;

    /**
     * @var bool Should this test use the legacy database for the tests?
     */
    const USELEGACYDATABASE = false;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::createDatabaseTable();
    }

    public static function tearDownAfterClass()
    {
        self::removeDatabaseTable();

        parent::tearDownAfterClass();
    }

    public function setUp()
    {
        parent::setUp();

        $this->initializeDatabase();
        $this->assureTestTableIsEmpty();
    }

    public function tearDown()
    {
        $this->assureTestTableIsEmpty();

        parent::tearDown();
    }

    /**
     * Create the database, we want to test.
     */
    protected function initializeDatabase()
    {
        $this->database = $this->createDatabase();
    }

    /**
     * Create the database object under test.
     *
     * @return Doctrine|oxLegacyDb The database object under test.
     */
    protected function createDatabase()
    {
        if (self::USELEGACYDATABASE) {
            return oxDb::getDb();
        } else {
            return new Doctrine();
        }
    }

    /**
     * Create the database object under test - the static pendant to use in the setUpBeforeClass and tearDownAfterClass.
     *
     * @return Doctrine|oxLegacyDb The database object under test.
     */
    protected static function createDatabaseStatic()
    {
        if (self::USELEGACYDATABASE) {
            return oxDb::getDb();
        } else {
            return new Doctrine();
        }
    }

    /**
     * Create a table in the database especially for this test.
     */
    protected static function createDatabaseTable()
    {
        $db = self::createDatabaseStatic();

        $db->execute('CREATE TABLE IF NOT EXISTS ' . self::TABLE_NAME . ' (oxid VARCHAR(32), oxuserid VARCHAR(32)) ENGINE innoDb;');
    }

    /**
     * Drop the test database table.
     */
    protected static function removeDatabaseTable()
    {
        $db = self::createDatabaseStatic();

        $db->execute('DROP TABLE ' . self::TABLE_NAME . ';');
    }

    /**
     * Load the test fixture to the oxdoctrinetest table.
     */
    protected function loadFixtureToTestTable()
    {
        $this->cleanTestTable();

        $values = array(
            self::FIXTURE_OXID_1 => self::FIXTURE_OXUSERID_1,
            self::FIXTURE_OXID_2 => self::FIXTURE_OXUSERID_2,
            self::FIXTURE_OXID_3 => self::FIXTURE_OXUSERID_3
        );

        $queryValuesParts = array();

        foreach ($values as $oxId => $oxUserId) {
            $queryValuesParts[] = "('$oxId','$oxUserId')";
        }

        $queryValuesPart = implode(',', $queryValuesParts);

        $query = "INSERT INTO " . self::TABLE_NAME . "(OXID, OXUSERID) VALUES $queryValuesPart;";

        $this->database->execute($query);
    }

    /**
     * Remove all rows from the oxdoctrinetest table.
     */
    protected function cleanTestTable()
    {
        $this->database->execute('DELETE FROM ' . self::TABLE_NAME . ';');
    }

    /**
     * Assure, that the table oxdoctrinetest is empty. If it is not empty, the test will fail.
     */
    protected function assureTestTableIsEmpty()
    {
        if (!$this->isEmptyTestTable()) {
            $this->cleanTestTable();
        }

        $this->assertEmpty($this->fetchAllTestTableRows(), "Problem while truncating the table '" . self::TABLE_NAME . "'!");
    }

    /**
     * Check, if the table oxdoctrinetest is empty.
     *
     * @return bool Is the table oxdoctrinetest empty?
     */
    protected function isEmptyTestTable()
    {
        return empty($this->fetchAllTestTableRows());
    }

    /**
     * Fetch all the rows of the oxdoctrinetest table.
     *
     * @return array All rows of the oxdoctrinetest table.
     */
    protected function fetchAllTestTableRows()
    {
        return $this->database->select('SELECT * FROM ' . self::TABLE_NAME)->getAll();
    }

}
