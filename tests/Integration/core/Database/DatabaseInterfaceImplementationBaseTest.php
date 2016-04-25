<?php
namespace OxidEsales\Eshop\Tests\integration\core\Database;

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

use oxDb;
use OxidEsales\Eshop\Core\Database\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Doctrine;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Base class for database integration tests.
 *
 * @group doctrine
 */
abstract class DatabaseInterfaceImplementationBaseTest extends UnitTestCase
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
     * @var bool Use the legacy database adapter.
     *
     * @todo get rid of this
     */
    const USE_LEGACY_DATABASE = false;

    /**
     * @var array Holds the errors caught by the user-defined error handler
     */
    protected $errors;

    /**
     * @var Doctrine|\oxLegacyDb The database to test.
     */
    protected $database = null;

    /**
     * Return the name of the database exception class
     */
    abstract protected function getDatabaseExceptionClassName();

    /**
     * Return the name of the database exception class
     */
    abstract protected function getResultSetClassName();

    /**
     * Return the name of the database exception class
     */
    abstract protected function getEmptyResultSetClassName();

    /**
     * Initialize database table before every test
     */
    public function setUp()
    {
        /** Set a user-defined error handler in order to handle errors triggered with trigger_error */
        $this->errors = array();
        set_error_handler(array($this, "errorHandler"));

        parent::setUp();

        $this->initializeDatabase();
        $this->assureTestTableIsEmpty();
    }

    /**
     * Provides an error handler
     *
     * @param integer $errorLevel   Error number as defined in http://php.net/manual/en/errorfunc.constants.php
     * @param string  $errorMessage Error message
     * @param string  $errorFile    Error file
     * @param integer $errorLine    Error line
     * @param array   $errorContext Error context
     */
    public function errorHandler($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext)
    {
        $this->errors[] = compact(
            "errorLevel",
            "errorMessage",
            "errorFile",
            "errorLine",
            "errorContext"
        );
    }

    /**
     * Assert a given error level and a given error message
     *
     * @param integer $errorLevel   Error number as defined in http://php.net/manual/en/errorfunc.constants.php
     * @param string  $errorMessage Error message
     *
     * @return boolean Returns true on assertion success
     */
    public function assertError($errorLevel, $errorMessage)
    {
        foreach ($this->errors as $error) {
            if ($error["errorMessage"] === $errorMessage
                && $error["errorLevel"] === $errorLevel
            ) {
                return true;
            }
        }
        $this->fail(
            "No error with level " . $errorLevel . " and message '" . $errorMessage . "' was triggered"
        );
    }


    /**
     * Empty database table after every test
     */
    public function tearDown()
    {
        $this->assureTestTableIsEmpty();
        $this->closeConnection();
        gc_collect_cycles();

        /** Restore the previous error handler function */
        restore_error_handler();
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
     * Create the database object under test - the static pendant to use in the setUpBeforeClass and tearDownAfterClass.
     *
     * @return Doctrine|\oxLegacyDb The database object under test.
     */
    abstract protected function createDatabase();

    /**
     * Hook function for closing the database connection.
     */
    abstract protected function closeConnection();


    /**
     * Load the test fixture to the oxdoctrinetest table.
     *
     * @param DatabaseInterface $database An instance of the database handler
     */
    protected function loadFixtureToTestTable($database = null)
    {
        if (is_null($database)) {
            $database = $this->database;
        }
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

        $database->execute($query);
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
     * Assure, that the table oxdoctrinetest has only the given oxId.
     *
     * @param string $oxId The oxId we want to be the only one in the oxdoctrinetest table.
     */
    protected function assertTestTableHasOnly($oxId)
    {
        $oxIds = $this->fetchAllTestTableRows();

        $this->assertNotEmpty($oxIds);
        $this->assertSame(1, count($oxIds));
        $this->assertArrayHasKey('0', $oxIds);

        $this->assertSame($oxId, $oxIds[0][0]);
    }

    /**
     * Assert, that the table oxdoctrinetest is empty.
     */
    protected function assertTestTableIsEmpty()
    {
        $this->assertTrue($this->isEmptyTestTable());
    }

    /**
     * Assert, that the given object has the wished attribute with the given value.
     *
     * @param object $object         The object we want to check for the given attribute.
     * @param string $attributeName  The name of the attribute we want to exist.
     * @param mixed  $attributeValue The wished value of the attribute.
     */
    protected function assertObjectHasAttributeWithValue($object, $attributeName, $attributeValue)
    {
        $this->assertTrue(isset($object->$attributeName), 'Missing field "' . $attributeName . '".');
        $this->assertSame($attributeValue, $object->$attributeName);
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
        return $this->database->select('SELECT * FROM ' . self::TABLE_NAME, array(), false)->getAll();
    }

    /**
     * Fetch the oxId of the first oxdoctrinetest table row.
     *
     * @return array|false The oxId of the first oxdoctrinetest table row.
     */
    protected function fetchFirstTestTableOxId()
    {
        $rows = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME, array(), false);
        $row = $rows->fetchRow();

        return $row;
    }
}
