<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Database\Adapter;

use OxidEsales\EshopCommunity\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Abstract base class for database integration tests.
 * Extend this class to have a common setup for low level database tests.
 *
 * @group database-adapter
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

    const EXPECTED_MYSQL_SYNTAX_ERROR_CODE  = 1064;
    const EXPECTED_MYSQL_SYNTAX_ERROR_MESSAGE  = 'You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near \'INVALID SQL QUERY\' at line 1';

    /**
     * @var array Holds the errors caught by the user-defined error handler
     */
    protected $errors;

    /**
     * @var DatabaseInterface The database to test.
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
     * Create the database object under test - the static pendant to use in the setUpBeforeClass and tearDownAfterClass.
     *
     * @return DatabaseInterface The database object under test.
     */
    abstract protected function createDatabase();

    /**
     * Hook function for closing the database connection.
     */
    abstract protected function closeConnection();

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
        $this->truncateTestTable();
        $this->assureTestTableIsEmpty();
    }

    /**
     * Set up before beginning with tests
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::createDatabaseTable();
    }

    /**
     * Empty database table after every test
     */
    public function tearDown()
    {
        $this->truncateTestTable();
        $this->closeConnection();
        gc_collect_cycles();

        /** Restore the previous error handler function */
        restore_error_handler();
        parent::tearDown();
    }

    /**
     * Tear down after all tests are done
     */
    public static function tearDownAfterClass()
    {
        self::removeDatabaseTable();

        parent::tearDownAfterClass();
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
     * Get a PDO instance representing a connection to the database.
     * Use this static method to access the database without using the shop adapters.
     *
     * @return \PDO PDO instance.
     */
    protected static function getDatabaseHandler()
    {
        $configFile = Registry::get('oxConfigFile');
        $dsn = 'mysql:host='. $configFile->getVar('dbHost') .';port='. $configFile->getVar('dbPort') .';dbname=' .$configFile->getVar('dbName') ;
        $username = $configFile->getVar('dbUser');
        $password = $configFile->getVar('dbPwd');

        $dbh = new \PDO($dsn, $username, $password);

        return $dbh;
    }

    /**
     * Create the database, we want to test.
     */
    protected function initializeDatabase()
    {
        $this->database = $this->createDatabase();
    }

    /**
     * Create the database table used for the integration tests.
     *
     * @return int
     */
    protected static function createDatabaseTable()
    {
        $dbh = self::getDatabaseHandler();

        return $dbh->exec('CREATE TABLE IF NOT EXISTS ' . self::TABLE_NAME . ' (oxid CHAR(32), oxuserid CHAR(32)) ENGINE innoDb;');
    }

    /**
     * Drop the test database table.
     *
     * @return int
     */
    protected static function removeDatabaseTable()
    {
        $dbh = self::getDatabaseHandler();

        return $dbh->exec('DROP TABLE ' . self::TABLE_NAME . ';');
    }

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
        $this->truncateTestTable();

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
     *
     * @return integer affected rows
     */
    protected function truncateTestTable()
    {
        return $this->database->execute('TRUNCATE ' . self::TABLE_NAME . ';');
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

    protected function assureTestTableIsEmpty()
    {
        $this->assertEmpty($this->fetchAllTestTableRows(), "Table '" . self::TABLE_NAME . "' is empty");
    }

    protected function fetchAllTestTableRows()
    {
        return $this->database->select('SELECT * FROM ' . self::TABLE_NAME)->fetchAll();
    }
}
