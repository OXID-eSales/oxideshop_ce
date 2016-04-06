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
    protected $useLegacyDatabase = false;

    public function setUp()
    {
        parent::setUp();

        $this->createDatabase();
        $this->assureOxVouchersTableIsEmpty();
    }

    public function tearDown()
    {
        $this->assureOxVouchersTableIsEmpty();

        parent::tearDown();
    }

    /**
     * Create the database, we want to test.
     */
    protected function createDatabase()
    {
        if ($this->useLegacyDatabase) {
            $this->database = oxDb::getDb();
        } else {
            $this->database = new Doctrine();
        }
    }

    /**
     * Load the test fixture to the oxvouchers table.
     */
    protected function loadFixtureToOxVouchersTable()
    {
        $this->cleanOxVouchersTable();

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

        $query = "INSERT INTO oxvouchers(OXID,OXUSERID) VALUES $queryValuesPart;";

        $this->database->execute($query);
    }

    /**
     * Remove all rows from the oxvoucher table.
     */
    protected function cleanOxVouchersTable()
    {
        $this->database->execute('DELETE FROM oxvouchers;');
    }

    /**
     * Assure, that the table oxvouchers is empty. If it is not empty, the test will fail.
     */
    protected function assureOxVouchersTableIsEmpty()
    {
        if (!$this->isEmptyOxVouchersTable()) {
            $this->cleanOxVouchersTable();
        }

        $this->assertEmpty($this->fetchAllOxVouchersRows(), "Problem while truncating the table 'oxvouchers'!");
    }

    /**
     * Fetch all the rows of the oxvouchers table.
     *
     * @return array All rows of the oxvouchers table.
     */
    protected function fetchAllOxVouchersRows()
    {
        return $this->database->select('SELECT * FROM oxvouchers')->getAll();
    }

    /**
     * Check, if the table oxvouchers is empty.
     *
     * @return bool Is the table oxvouchers empty?
     */
    protected function isEmptyOxVouchersTable()
    {
        return empty($this->fetchAllOxVouchersRows());
    }

}
