<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxDb;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;
use ReflectionClass;

/**
 * Class DbTest
 *
 * @group   database-adapter
 * @covers  \OxidEsales\Eshop\Core\DatabaseProvider
 * @package Unit\Core
 */
class DatabaseTest extends UnitTestCase
{
    /**
     * Clean-up oxarticles table + parent::tearDown()
     */
    protected function tearDown()
    {
        $configFile = new \OxidEsales\Eshop\Core\ConfigFile(OX_BASE_PATH . 'config.inc.php');
        Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFile);

        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }


    /**
     * Call a given protected method on an given instance of a class and return the result.
     *
     * @param object $classInstance Instance of the class on which the method will be called
     * @param string $methodName    Name of the method to be called
     * @param array  $params        Parameters of the method to be called
     *
     * @return mixed
     */
    protected function callProtectedClassMethod($classInstance, $methodName, array $params = array())
    {
        $className = get_class($classInstance);

        $reflectionClass = new ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($classInstance, $params);
    }

    public function testSetConfig()
    {
        $debug = 7;

        $configFile = $this->getBlankConfigFile();
        $configFile->iDebug = $debug;

        $database = oxDb::getInstance();
        $database->setConfigFile($configFile);

        $actualResult = $this->callProtectedClassMethod($database, 'getConfigParam', array('iDebug'));

        $this->assertEquals($debug, $actualResult, 'Result of getConfigParam(iDebug) should match value in config.inc.php');

        $debug = 8;
        $configFile->iDebug = $debug;
        $database->setConfigFile($configFile);
        $actualResult = $this->callProtectedClassMethod($database, 'getConfigParam', array('iDebug'));

        $this->assertEquals($debug, $actualResult, 'Result of getConfigParam(iDebug) should match value in config.inc.php');
    }

    public function testGetTableDescription()
    {
        /** Reset the table description cache */
        $database = oxDb::getInstance();
        $database->flushTableDescriptionCache();

        $resultSet = oxDb::getDb()->select("SHOW TABLES");
        $count = 3;
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF && $count--) {
                $tableName = $resultSet->fields[0];

                $metaColumns = oxDb::getDb()->metaColumns($tableName);
                $metaColumnOne = oxDb::getInstance()->getTableDescription($tableName);
                $metaColumnOneCached = oxDb::getInstance()->getTableDescription($tableName);

                $this->assertEquals($metaColumns, $metaColumnOne, "not cached return is bad [shouldn't be] of $tableName.");
                $this->assertEquals($metaColumns, $metaColumnOneCached, "cached [simple] return is bad of $tableName.");

                $resultSet->fetchRow();
            }
        } else {
            $this->fail("No tables found with 'SHOW TABLES'!");
        }
    }

    public function testGetInstanceReturnsInstanceOfDatabase()
    {
        $database = oxDb::getInstance();

        $this->assertInstanceOf(DatabaseProvider::class, $database);
    }

    public function testGetDbReturnsAnInstanceOfDatabaseInterface()
    {
        $database = oxDb::getDb();

        $this->assertInstanceOf(DatabaseInterface::class, $database);
    }

    public function testGetDbReturnsAnInstanceOfDoctrine()
    {
        $database = oxDb::getDb();

        $this->assertInstanceOf(Database::class, $database);
    }

    /**
     * Helper methods
     */

    /**
     * @return \OxidEsales\Eshop\Core\ConfigFile
     */
    protected function getBlankConfigFile()
    {
        return new \OxidEsales\Eshop\Core\ConfigFile($this->createFile('config.inc.php', '<?php '));
    }
}
