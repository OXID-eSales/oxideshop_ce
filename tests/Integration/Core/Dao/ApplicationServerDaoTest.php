<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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

    public function testFindAppServerIfExists()
    {
        $this->storeInitialServersData();

        $appServerDao = $this->getApplicationServerDaoObject();
        $appServer = $appServerDao->findAppServer('serverNameHash1');

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash1');
        $expectedServer->setTimestamp('timestamp');
        $expectedServer->setIp('127.0.0.1');
        $expectedServer->setLastFrontendUsage('frontendUsageTimestamp');

        $this->assertEquals($expectedServer, $appServer);
    }

    public function testFindAppServerIfNotExists()
    {
        $this->storeInitialServersData();

        $appServerDao = $this->getApplicationServerDaoObject();
        $appServer = $appServerDao->findAppServer('serverNameHash3');

        $this->assertNull($appServer);
    }

    public function testDeleteExisting()
    {
        $this->storeInitialServersData();

        $appServerDao = $this->getApplicationServerDaoObject();
        $appServerDao->delete('serverNameHash1');

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
        $appServerDao->delete('serverNameHash3');

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

        $appServerDao = $this->getApplicationServerDaoObject();
        $appServerDao->save($expectedServer);

        $appServer = $appServerDao->findAppServer('serverNameHash1');
        $this->assertEquals($expectedServer, $appServer);
    }

    public function testUpdateNotExisting()
    {
        $this->storeInitialServersData();

        $updateServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $updateServer->setId('serverNameHash3');
        $updateServer->setTimestamp('timestamp');
        $updateServer->setIp('127.0.0.1');
        $updateServer->setLastAdminUsage('updatedAdminUsageTimestamp');

        $appServerDao = $this->getApplicationServerDaoObject();
        $appServerDao->save($updateServer);

        $expectedServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $expectedServer->setId('serverNameHash1');
        $expectedServer->setTimestamp('timestamp');
        $expectedServer->setIp('127.0.0.1');
        $expectedServer->setLastFrontendUsage('frontendUsageTimestamp');

        $appServer = $appServerDao->findAppServer('serverNameHash1');
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

        $appServerDao->save($expectedServer);

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

        $appServerDao->save($expectedServer);

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
            'lastAdminUsage'    => ''
        );
        $storedData2 = array(
            'id'                => 'serverNameHash2',
            'timestamp'         => 'timestamp',
            'ip'                => '127.0.0.2',
            'lastFrontendUsage' => '',
            'lastAdminUsage'    => 'adminUsageTimestamp'
        );
        $this->getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', $storedData1);
        $this->getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash2', $storedData2);
    }

    private function getApplicationServerDaoObject()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $config = $this->getConfig();

        return oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $database, $config);
    }
}
