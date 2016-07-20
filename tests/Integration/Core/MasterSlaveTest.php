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

namespace OxidEsales\Eshop\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Database;
use OxidEsales\EshopEnterprise\Core\Database as EnterpriseDatabase;
use OxidEsales\TestingLibrary\UnitTestCase;
use ReflectionClass;

/**
 * Class MasterSlaveTest
 *
 * @covers OxidEsales\Eshop\Core\Database
 */
class MasterSlaveTest extends UnitTestCase
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

        $this->setProtectedClassProperty(Database::getInstance(), 'db', null);
        $this->assertNull($this->getProtectedClassProperty(Database::getInstance(), 'db'));
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
     * Test case that we have no master slave setup.
     */
    public function testGetDb()
    {
        if('EE' == $this->getTestConfig()->getShopEdition()) {
            $this->markTestSkipped('Test is for CE/PE only.');
        }

        $connection = Database::getDb();
        $this->assertTrue(is_a($connection, 'OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database'));

        $dbConnection = $this->getProtectedClassProperty($connection, 'connection');
        $this->assertTrue(is_a($dbConnection, 'Doctrine\DBAL\Connection'));
        $this->assertFalse(is_a($dbConnection, 'Doctrine\DBAL\Connections\MasterSlaveConnection'));
        $this->assertSame($this->getConfig()->getConfigParam('dbHost'), $dbConnection->getHost());
    }

    /**
     * Test case that we have no master slave setup.
     */
    public function testGetMasterNoMasterSlaveSetup()
    {
        if('EE' == $this->getTestConfig()->getShopEdition()) {
            $this->markTestSkipped('Test is for CE/PE only.');
        }

        $connection = Database::getMaster();
        $this->assertTrue(is_a($connection, 'OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database'));

        $dbConnection = $this->getProtectedClassProperty($connection, 'connection');
        $this->assertTrue(is_a($dbConnection, 'Doctrine\DBAL\Connection'));
        $this->assertFalse(is_a($dbConnection, 'Doctrine\DBAL\Connections\MasterSlaveConnection'));
        $this->assertSame($this->getConfig()->getConfigParam('dbHost'), $dbConnection->getHost());
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
     *
     * @return mixed
     */
    protected function getProtectedClassProperty($classInstance, $property)
    {
        $className = get_class($classInstance);

        $reflectionClass = new ReflectionClass($className);

        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($classInstance);
    }

}
