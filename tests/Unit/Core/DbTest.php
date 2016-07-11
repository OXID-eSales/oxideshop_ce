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
 *
 * @group   database-adapter
 * @covers  OxidEsales\Eshop\Core\Database
 * @package Unit\Core
 */
class DbTest extends UnitTestCase
{

    /**
     * Clean-up oxarticles table + parent::tearDown()
     */
    protected function tearDown()
    {
        $configFile = new ConfigFile(OX_BASE_PATH . 'config.inc.php');
        Registry::set('oxConfigFile', $configFile);

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
        $this->setProtectedClassProperty(Database::getInstance(), 'tblDescCache', []);

        $resultSet = Database::getDb()->select("SHOW TABLES");
        $count = 3;
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF && $count--) {
                $tableName = $resultSet->fields[0];

                $metaColumns = Database::getDb()->metaColumns($tableName);
                $metaColumnOne = Database::getInstance()->getTableDescription($tableName);
                $metaColumnOneCached = Database::getInstance()->getTableDescription($tableName);

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
        $database = Database::getInstance();

        $this->assertInstanceOf('OxidEsales\Eshop\Core\Database', $database);
    }

    public function testGetDbReturnsAnInstanceOfDatabaseInterface()
    {
        $database = Database::getDb();

        $this->assertInstanceOf('OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface', $database);
    }

    public function testGetDbReturnsAnInstanceOfDoctrine()
    {
        $database = Database::getDb();

        $this->assertInstanceOf('OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database', $database);
    }

    public function testGetDbThrowsDatabaseConnectionException()
    {
        /** Set an invalid config file */
        $configFile = $this->getBlankConfigFile();
        Registry::set('oxConfigFile', $configFile);
        /** Reset the db property */
        $this->setProtectedClassProperty(Database::getInstance(), 'db', null);

        $this->setExpectedException('OxidEsales\Eshop\Core\Exception\DatabaseConnectionException');

        Database::getDb();
    }

    public function testGetDbThrowsDatabaseNotConfiguredException()
    {
        /** Set an invalid config file */
        $configFile = $this->getBlankConfigFile();
        $configFile->setVar('dbHost', '<');
        Registry::set('oxConfigFile', $configFile);
        /** Reset the db property */
        $this->setProtectedClassProperty(Database::getInstance(), 'db', null);

        $this->setExpectedException('OxidEsales\Eshop\Core\Exception\DatabaseNotConfiguredException');

        Database::getDb();
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
}
