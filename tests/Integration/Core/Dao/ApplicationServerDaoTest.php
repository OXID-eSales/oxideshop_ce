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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * @covers \OxidEsales\Eshop\Core\Dao\ApplicationServerDao
 */
class ApplicationServerDaoTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function setUp()
    {
        parent::setUp();
        DatabaseProvider::getDb()->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");
    }

    public function testFindAll()
    {
        $this->storeInitialServersData();

        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        $appServers = $appServerDao->findAll();

        $this->assertEquals(2, count($appServers));

        foreach ($appServers as $appServer) {
            $this->assertInstanceOf('\OxidEsales\Eshop\Core\DataObject\ApplicationServer', $appServer);
        }
    }

    public function testFindAllNoneExists()
    {
        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        $appServers = $appServerDao->findAll();

        $this->assertEquals(0, count($appServers));
    }

    public function testFindByIdIfExists()
    {
        $this->storeInitialServersData();

        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
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

        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
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

        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        $result = $appServerDao->delete('serverNameHash1');
        $this->assertEquals(1, $result);

        $appServers = $appServerDao->findAll();
        $this->assertEquals(1, count($appServers));

        $appServer = $appServers['serverNameHash2'];

        $this->assertEquals('serverNameHash2', $appServer->getId());
    }

    public function testDeleteNotExisting()
    {
        $this->storeInitialServersData();

        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        $result = $appServerDao->delete('serverNameHash3');
        $this->assertEquals(0, $result);

        $appServers = $appServerDao->findAll();
        $this->assertEquals(2, count($appServers));
    }

    public function testUpdateExisting()
    {
        $this->storeInitialServersData();

        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash1');
        $expectedServer->setTimestamp('timestamp');
        $expectedServer->setIp('127.0.0.1');
        $expectedServer->setLastFrontendUsage('updatedFrontendUsageTimestamp');
        $expectedServer->setLastAdminUsage('updatedAdminUsageTimestamp');
        $expectedServer->setIsValid();

        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        $result = $appServerDao->update($expectedServer);

        $this->assertEquals(1, $result);

        $appServer = $appServerDao->findById('serverNameHash1');
        $this->assertEquals($expectedServer, $appServer);
    }

    public function testUpdateNotExisting()
    {
        $this->storeInitialServersData();

        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();

        $updateServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $updateServer->setId('serverNameHash3');
        $updateServer->setTimestamp('timestamp');
        $updateServer->setIp('127.0.0.1');
        $updateServer->setLastFrontendUsage('updatedFrontendUsageTimestamp');
        $updateServer->setLastAdminUsage('updatedAdminUsageTimestamp');
        $updateServer->setIsValid();

        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
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
        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();

        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
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

        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();

        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        $appServers = $appServerDao->findAll();

        $this->assertEquals(2, count($appServers));

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash1');
        $expectedServer->setTimestamp('timestamp');
        $expectedServer->setIp('127.0.0.1');
        $expectedServer->setLastFrontendUsage('frontendUsageTimestamp');
        $expectedServer->setLastAdminUsage('adminUsageTimestamp');
        $expectedServer->setIsValid();

        $result = $appServerDao->insert($expectedServer);

        $appServers = $appServerDao->findAll();
        $this->assertEquals(2, count($appServers));
    }

    private function storeInitialServersData()
    {
        $aStoredData1 = array(
            'id'                => 'serverNameHash1',
            'timestamp'         => 'timestamp',
            'ip'                => '127.0.0.1',
            'lastFrontendUsage' => 'frontendUsageTimestamp',
            'lastAdminUsage'    => 'adminUsageTimestamp',
            'isValid'           => true
        );
        $aStoredData2 = array(
            'id'                => 'serverNameHash2',
            'timestamp'         => 'timestamp',
            'ip'                => '127.0.0.2',
            'lastFrontendUsage' => 'frontendUsageTimestamp',
            'lastAdminUsage'    => 'adminUsageTimestamp',
            'isValid'           => true
        );
        Registry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', $aStoredData1);
        Registry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash2', $aStoredData2);
    }

}
