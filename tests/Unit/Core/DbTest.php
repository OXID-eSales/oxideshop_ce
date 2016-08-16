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
namespace Unit\Core;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Database;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;
use ReflectionClass;
use OxidEsales\Eshop\Core\exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\ShopIdCalculator;

/**
 * Class DbTest
 * TODO rename to DatabaseTest
 * @group database-adapter
 * @covers OxidEsales\Eshop\Core\Database
 * @package Unit\Core
 */
class DbTest extends UnitTestCase
{
    protected function setUp() {
        parent::setUp();

        $database = Database::getInstance();
        $database->setConfigFile(Registry::get('oxConfigFile'));
    }

    /**
     * Clean-up oxarticles table + parent::tearDown()
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }

    public function testSetConfig()
    {
        $debug = 7;

        $configFile = $this->getBlankConfigFile();
        $configFile->iDebug = $debug;

        $database = Database::getInstance();
        $database->setConfigFile($configFile);
        $methodGetConfigParam = self::getReflectedMethod('getConfigParam');

        $actualResult = $methodGetConfigParam->invokeArgs($database, array('iDebug'));

        $this->assertEquals($debug, $actualResult, 'Result of getConfigParam(iDebug) should match value in config.inc.php');

        $debug = 8;
        $configFile->iDebug = $debug;
        $database->setConfigFile($configFile);
        $methodGetConfigParam = self::getReflectedMethod('getConfigParam');

        $actualResult = $methodGetConfigParam->invokeArgs($database, array('iDebug'));

        $this->assertEquals($debug, $actualResult, 'Result of getConfigParam(iDebug) should match value in config.inc.php');
    }

    public function testSetDbObject()
    {
        $database = Database::getInstance();
        $dbMock = $this->getDbObjectMock();

        $database->setDbObject($dbMock);

        $realResult = $database->getDb();
        $this->assertEquals($dbMock, $realResult);
    }

    public function testGetDbObject()
    {
        $database = Database::getInstance();
        $dbMock = $this->getDbObjectMock();

        $database->setDbObject($dbMock);

        $realResult = $database->getDbObject();
        $this->assertEquals($dbMock, $realResult);
    }

    public function testGetTableDescription()
    {
        self::callMethod('resetTblDescCache');

        $rs = Database::getDb()->execute("show tables");
        $icount = 3;
        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF && $icount--) {
                $sTable = $rs->fields[0];

                $amc = Database::getDb()->metaColumns($sTable);
                $rmc1 = Database::getInstance()->getTableDescription($sTable);
                $rmc2 = Database::getInstance()->getTableDescription($sTable);

                $this->assertEquals($amc, $rmc1, "not cached return is bad [shouldn't be] of $sTable.");
                $this->assertEquals($amc, $rmc2, "cached [simple] return is bad of $sTable.");

                $rs->MoveNext();
            }
        } else {
            $this->fail("no tables???");
        }
    }

    public function testIsValidFieldName()
    {
        $database = Database::getInstance();

        $this->assertTrue($database->isValidFieldName('oxid'));
        $this->assertTrue($database->isValidFieldName('oxid_1'));
        $this->assertTrue($database->isValidFieldName('oxid.1'));
        $this->assertFalse($database->isValidFieldName('oxid{1'));
    }

    /**
     * Testing escaping string
     * Todo Remove when deprecated in 5.3
     */
    public function testEscapeString()
    {
        $sString = "\x00 \n \r ' \, \" \x1a";

        $database = Database::getInstance();

        $this->assertEquals('\0 \n \r \\\' \\\, \" \Z', $database->escapeString($sString));

    }

    public function testGetInstanceReturnsInstanceOfDatabase()
    {
        $database = Database::getInstance();

        $this->assertInstanceOf('OxidEsales\Eshop\Core\Database', $database);
    }

    public function testGetDbReturnsAnInstanceOfDatabaseInterface()
    {
        $database = Database::getDb();

        $this->assertInstanceOf('OxidEsales\Eshop\Core\Database\DatabaseInterface', $database);
    }

    public function testGetDbReturnsAnInstanceOfDoctrine()
    {
        $database = Database::getDb();

        $this->assertInstanceOf('OxidEsales\Eshop\Core\Database\Doctrine', $database);
    }

    /**
     * Helper methods
     */

    /**
     * @return ConfigFile
     */
    protected function getBlankConfigFile()
    {
        return new ConfigFile($this->createFile('config.inc.php', '<?php '));
    }

    /**
     * @param $methodName
     * @param $params
     */
    protected static function callMethod($methodName, array $params = array())
    {
        $class = new Database();
        $reflectedMethod = self::getReflectedMethod($methodName);

        return $reflectedMethod->invokeArgs($class, $params);
    }

    /**
     * Helper method for accessing protected class methods
     *
     * @param string $name Name of the protected method
     *
     * @return mixed The reflected method
     */
    protected static function getReflectedMethod($name)
    {
        $class = new ReflectionClass('OxidEsales\Eshop\Core\Database');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public static function resetDbProperty($class) {
        $reflectionClass = new ReflectionClass('OxidEsales\Eshop\Core\Database');

        $reflectionProperty = $reflectionClass->getProperty('db');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($class, null);

    }
}
