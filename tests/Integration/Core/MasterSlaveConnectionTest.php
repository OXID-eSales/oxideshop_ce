<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use oxDb;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class MasterSlaveConnectionTest
 *
 * @covers \OxidEsales\EshopCommunity\Core\DatabaseProvider
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
        if ('EE' == $this->getTestConfig()->getShopEdition()) {
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
        if ('EE' == $this->getTestConfig()->getShopEdition()) {
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
        if ('EE' == $this->getTestConfig()->getShopEdition()) {
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
        if ('EE' == $this->getTestConfig()->getShopEdition()) {
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
