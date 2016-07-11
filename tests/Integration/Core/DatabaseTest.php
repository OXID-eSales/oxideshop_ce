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

namespace Integration\Core;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Database;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;
use ReflectionClass;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseNotConfiguredException;

/**
 * Class DatabaseTest
 * 
 * @group database-adapter
 * @covers OxidEsales\Eshop\Core\Database
 */
class DatabaseTest extends UnitTestCase
{
    /** @var mixed Backing up for earlier value of database link object */
    private $dbObjectBackup = null;

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->dbObjectBackup = $this->getProtectedClassProperty(Database::getInstance(), 'db');
    }

    /**
     * Executed after test is down.
     */
    protected function tearDown()
    {
        Database::getDb()->closeConnection();

        $this->setProtectedClassProperty(Database::getInstance(), 'db', $this->dbObjectBackup);
        
        Database::getDb()->closeConnection();

        parent::tearDown();
    }

    /**
     * Set a given protected property of a given class instance to a given value.
     *
     * @param object $classInstance Instance of the class of which the property will be set
     * @param string $property      Name of the property to be set
     * @param mixed  $value         Value to which the property will be set
     */
    protected function setProtectedClassProperty($classInstance, $property, $value)
    {
        $className = get_class($classInstance);

        $reflectionClass = new ReflectionClass($className);

        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($classInstance, $value);
    }

    /**
     * Get a given protected property of a given class instance.
     *
     * @param object $classInstance Instance of the class of which the property will be set
     * @param string $property      Name of the property to be retrieved
     */
    protected function getProtectedClassProperty($classInstance, $property)
    {
        $className = get_class($classInstance);

        $reflectionClass = new ReflectionClass($className);

        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->getValue($classInstance);
    }

    public function testGetDbThrowsDatabaseConnectionException()
    {
        /** @var ConfigFile $configFileBackup Backup of the configFile as stored in Registry. This object must be restored  */
        $configFileBackup = Registry::get('oxConfigFile');

        $configFile = $this->getBlankConfigFile();
        Registry::set('oxConfigFile',$configFile);
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , null);

        $this->setExpectedException('OxidEsales\Eshop\Core\Exception\DatabaseConnectionException');

        try {
            Database::getDb();
        } catch (DatabaseConnectionException $exception ) {
            /** Restore original configFile object */
            Registry::set('oxConfigFile',$configFileBackup);
            throw $exception;
        }
    }

    public function testGetDbThrowsDatabaseNotConfiguredException()
    {
        /** @var ConfigFile $configFileBackup Backup of the configFile as stored in Registry. This object must be restored  */
        $configFileBackup = Registry::get('oxConfigFile');

        $configFile = $this->getBlankConfigFile();
        $configFile->setVar('dbHost','<');
        Registry::set('oxConfigFile',$configFile);
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , null);

        $this->setExpectedException('OxidEsales\Eshop\Core\Exception\DatabaseNotConfiguredException');
        
        try {
            Database::getDb();            
        } catch (DatabaseNotConfiguredException $exception ) {
            /** Restore original configFile object */
            Registry::set('oxConfigFile',$configFileBackup);
            throw $exception;
        }
    }

    /**
     * Test, that the cache will not 
     */
    public function testUnflushedCacheDoesntWorks()
    {
        $database = Database::getInstance();
        $connection = Database::getDb();

        $connection->execute('CREATE TABLE IF NOT EXISTS TEST(OXID char(32));');

        $tableDescription = $database->getTableDescription('TEST');

        $connection->execute('ALTER TABLE TEST ADD OXTITLE CHAR(32); ');

        $tableDescriptionTwo = $database->getTableDescription('TEST');

        // clean up
        $connection->execute('DROP TABLE IF EXISTS TEST;');

        $this->assertSame(1, count($tableDescription));
        $this->assertSame(1, count($tableDescriptionTwo));
    }

    /**
     * Test, that the flushing really refreshes the table description cache.
     */
    public function testFlushedCacheHasActualInformation()
    {
        $database = Database::getInstance();
        $connection = Database::getDb();

        $connection->execute('CREATE TABLE IF NOT EXISTS TEST(OXID char(32));');

        $tableDescription = $database->getTableDescription('TEST');

        $connection->execute('ALTER TABLE TEST ADD OXTITLE CHAR(32); ');

        $database->flushTableDescriptionCache();
        
        $tableDescriptionTwo = $database->getTableDescription('TEST');

        // clean up
        $connection->execute('DROP TABLE IF EXISTS TEST;');

        $this->assertSame(1, count($tableDescription));
        $this->assertSame(2, count($tableDescriptionTwo));
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
