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

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use oxDb;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class MasterSlaveConnectionTest
 *
 * @covers \OxidEsales\EshopCommunity\Core\Database
 */
class MasterSlaveConnectionTest extends UnitTestCase
{
    /** @var mixed Backing up for earlier value of database link object */
    private $dbObjectBackup = null;

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->dbObjectBackup = $this->getProtectedClassProperty(oxDb::getInstance(), 'db');

        $this->setProtectedClassProperty(oxDb::getInstance(), 'db', null);
        $this->assertNull($this->getProtectedClassProperty(oxDb::getInstance(), 'db'));
    }

    /**
     * Executed after test is down.
     */
    protected function tearDown()
    {
        oxDb::getDb()->closeConnection();

        $this->setProtectedClassProperty(oxDb::getInstance(), 'db', $this->dbObjectBackup);

        oxDb::getDb()->closeConnection();

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

        $connection = oxDb::getDb();
        $this->assertTrue(is_a($connection, 'OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database'));

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

        $connection = oxDb::getMaster();
        $this->assertTrue(is_a($connection, 'OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database'));

        $dbConnection = $this->getProtectedClassProperty($connection, 'connection');
        $this->assertTrue(is_a($dbConnection, 'Doctrine\DBAL\Connection'));
        $this->assertFalse(is_a($dbConnection, 'Doctrine\DBAL\Connections\MasterSlaveConnection'));
        $this->assertSame($this->getConfig()->getConfigParam('dbHost'), $dbConnection->getHost());
    }

    /**
     * Test case that we have no master slave setup and force master.
     */
    public function testForceMasterNoMasterSlaveSetup()
    {
        if('EE' == $this->getTestConfig()->getShopEdition()) {
            $this->markTestSkipped('Test is for CE/PE only.');
        }

        $connection = oxDb::getDb();
        $connection->forceMasterConnection();
        $this->assertTrue(is_a($connection, 'OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database'));

        $dbConnection = $this->getProtectedClassProperty($connection, 'connection');
        $this->assertTrue(is_a($dbConnection, 'Doctrine\DBAL\Connection'));
        $this->assertFalse(is_a($dbConnection, 'Doctrine\DBAL\Connections\MasterSlaveConnection'));
        $this->assertSame($this->getConfig()->getConfigParam('dbHost'), $dbConnection->getHost());
    }

    /**
     * Test case that we have no master slave setup and force slave.
     */
    public function testForceSlaveNoMasterSlaveSetup()
    {
        if('EE' == $this->getTestConfig()->getShopEdition()) {
            $this->markTestSkipped('Test is for CE/PE only.');
        }

        $connection = oxDb::getDb();
        $connection->forceSlaveConnection();
        $this->assertTrue(is_a($connection, 'OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database'));

        $dbConnection = $this->getProtectedClassProperty($connection, 'connection');
        $this->assertTrue(is_a($dbConnection, 'Doctrine\DBAL\Connection'));
        $this->assertFalse(is_a($dbConnection, 'Doctrine\DBAL\Connections\MasterSlaveConnection'));
        $this->assertSame($this->getConfig()->getConfigParam('dbHost'), $dbConnection->getHost());
    }

}
