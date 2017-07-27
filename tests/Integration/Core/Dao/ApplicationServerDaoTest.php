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
namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Dao;

/**
 * @covers \OxidEsales\EshopCommunity\Core\Dao\ApplicationServerDao
 */
class ApplicationServerDaoTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function setUp()
    {
        parent::setUp();
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");
    }

    public function testFindAll()
    {
        $this->storeInitialServersData();

        $appServerDao = $this->getApplicationServerDaoObject();
        $appServers = $appServerDao->findAll();

        $this->assertEquals(2, count($appServers));

        foreach ($appServers as $appServer) {
            $this->assertInstanceOf('\OxidEsales\Eshop\Core\DataObject\ApplicationServer', $appServer);
        }
    }

    public function testFindAllNoneExists()
    {
        $appServerDao = $this->getApplicationServerDaoObject();
        $appServers = $appServerDao->findAll();

        $this->assertEquals(0, count($appServers));
    }

    public function testFindByIdIfExists()
    {
        $this->storeInitialServersData();

        $appServerDao = $this->getApplicationServerDaoObject();
        $appServer = $appServerDao->findById('serverNameHash1');

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash1');
        $expectedServer->setTimestamp('timestamp');
        $expectedServer->setIp('127.0.0.1');
        $expectedServer->setLastFrontendUsage('frontendUsageTimestamp');
        $expectedServer->setLastAdminUsage('adminUsageTimestamp');
        $expectedServer->setIsValid();

        $this->assertEquals($expectedServer, $appServer);
    }

    public function testFindByIdIfNotExists()
    {
        $this->storeInitialServersData();

        $appServerDao = $this->getApplicationServerDaoObject();
        $appServer = $appServerDao->findById('serverNameHash3');

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash3');
        $expectedServer->setTimestamp(null);
        $expectedServer->setIp(null);
        $expectedServer->setLastFrontendUsage(null);
        $expectedServer->setLastAdminUsage(null);
        $expectedServer->setIsValid(false);

        $this->assertEquals($expectedServer, $appServer);
    }

    public function testDeleteExisting()
    {
        $this->storeInitialServersData();

        $appServerDao = $this->getApplicationServerDaoObject();
        $result = $appServerDao->delete('serverNameHash1');
        $this->assertEquals(1, $result);

        $appServers = $appServerDao->findAll();
        $this->assertEquals(1, count($appServers));

        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer */
        $appServer = $appServers['serverNameHash2'];

        $this->assertEquals('serverNameHash2', $appServer->getId());
    }

    public function testDeleteNotExisting()
    {
        $this->storeInitialServersData();

        $appServerDao = $this->getApplicationServerDaoObject();
        $result = $appServerDao->delete('serverNameHash3');
        $this->assertEquals(0, $result);

        $appServers = $appServerDao->findAll();
        $this->assertEquals(2, count($appServers));
    }

    public function testUpdateExisting()
    {
        $this->storeInitialServersData();

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash1');
        $expectedServer->setTimestamp('timestamp');
        $expectedServer->setIp('127.0.0.1');
        $expectedServer->setLastFrontendUsage('updatedFrontendUsageTimestamp');
        $expectedServer->setLastAdminUsage('updatedAdminUsageTimestamp');
        $expectedServer->setIsValid();

        $appServerDao = $this->getApplicationServerDaoObject();
        $result = $appServerDao->update($expectedServer);

        $this->assertEquals(1, $result);

        $appServer = $appServerDao->findById('serverNameHash1');
        $this->assertEquals($expectedServer, $appServer);
    }

    public function testUpdateNotExisting()
    {
        $this->storeInitialServersData();

        $updateServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $updateServer->setId('serverNameHash3');
        $updateServer->setTimestamp('timestamp');
        $updateServer->setIp('127.0.0.1');
        $updateServer->setLastFrontendUsage('updatedFrontendUsageTimestamp');
        $updateServer->setLastAdminUsage('updatedAdminUsageTimestamp');
        $updateServer->setIsValid();

        $appServerDao = $this->getApplicationServerDaoObject();
        $result = $appServerDao->update($updateServer);

        $this->assertEquals(0, $result);

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash1');
        $expectedServer->setTimestamp('timestamp');
        $expectedServer->setIp('127.0.0.1');
        $expectedServer->setLastFrontendUsage('frontendUsageTimestamp');
        $expectedServer->setLastAdminUsage('adminUsageTimestamp');
        $expectedServer->setIsValid();

        $appServer = $appServerDao->findById('serverNameHash1');
        $this->assertEquals($expectedServer, $appServer);
    }

    public function testInsertNew()
    {
        $appServerDao = $this->getApplicationServerDaoObject();
        $appServers = $appServerDao->findAll();

        $this->assertEquals(0, count($appServers));

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash1');
        $expectedServer->setTimestamp('timestamp');
        $expectedServer->setIp('127.0.0.1');
        $expectedServer->setLastFrontendUsage('frontendUsageTimestamp');
        $expectedServer->setLastAdminUsage('adminUsageTimestamp');
        $expectedServer->setIsValid();

        $result = $appServerDao->insert($expectedServer);
        $this->assertEquals(1, $result);

        $appServers = $appServerDao->findAll();
        $this->assertEquals(1, count($appServers));
    }

    public function testInsertExisting()
    {
        $this->storeInitialServersData();

        $appServerDao = $this->getApplicationServerDaoObject();
        $appServers = $appServerDao->findAll();

        $this->assertEquals(2, count($appServers));

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash1');
        $expectedServer->setTimestamp('timestamp');
        $expectedServer->setIp('127.0.0.1');
        $expectedServer->setLastFrontendUsage('frontendUsageTimestamp');
        $expectedServer->setLastAdminUsage('adminUsageTimestamp');
        $expectedServer->setIsValid();

        $appServerDao->insert($expectedServer);

        $appServers = $appServerDao->findAll();
        $this->assertEquals(2, count($appServers));
    }

    private function storeInitialServersData()
    {
        $storedData1 = array(
            'id'                => 'serverNameHash1',
            'timestamp'         => 'timestamp',
            'ip'                => '127.0.0.1',
            'lastFrontendUsage' => 'frontendUsageTimestamp',
            'lastAdminUsage'    => 'adminUsageTimestamp',
            'isValid'           => true
        );
        $storedData2 = array(
            'id'                => 'serverNameHash2',
            'timestamp'         => 'timestamp',
            'ip'                => '127.0.0.2',
            'lastFrontendUsage' => 'frontendUsageTimestamp',
            'lastAdminUsage'    => 'adminUsageTimestamp',
            'isValid'           => true
        );
        $this->getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', $storedData1);
        $this->getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash2', $storedData2);
    }

    private function getApplicationServerDaoObject()
    {
        $databaseProvider = oxNew(\OxidEsales\Eshop\Core\DatabaseProvider::class);
        $config = $this->getConfig();

        return oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
    }
}
