<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\OnlineInfo;

use OxidEsales\Eshop\Core\Dao\ApplicationServerDao;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\DataObject\ApplicationServer;
use OxidEsales\Eshop\Core\Service\ApplicationServerService;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class FrontendServersInformationStoringTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DatabaseProvider::getDb()->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");
    }

    /**
     * Add first new application server in frontend.
     */
    public function testUpdateAppServerInformationNewAppServer(): void
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get('oxUtilsDate')->getTime();

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInFrontend();

        /** @var ApplicationServer[] $appServers */
        $appServers = $service->loadAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertEquals('serverNameHash1', $appServers['serverNameHash1']->getId());
    }

    /**
     * There is one up to date application server, so no data to update.
     */
    public function testUpdateAppServerInformationAppServerExists(): void
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get('oxUtilsDate')->getTime();

        $this->storeAppServer1Information(($currentTime - (11 * 3600)));

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInFrontend();

        /** @var ApplicationServer[] $appServers */
        $appServers = $service->loadAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertEquals('adminUsageTimestampUpdated', $appServers['serverNameHash1']->getLastAdminUsage());
    }

    /**
     * There is one not active application server, the information of this server must be updated.
     */
    public function testUpdateAppServerInformationUpdateAppServerData(): void
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get('oxUtilsDate')->getTime();

        $this->storeAppServer1Information(($currentTime - (25 * 3600)));

        $service = $this->getApplicationServerServiceObject($currentTime);
        $appServers = $service->loadActiveAppServerList();

        $this->assertEquals(0, count($appServers));

        $service->updateAppServerInformationInFrontend();

        /** @var ApplicationServer[] $appServers */
        $appServers = $service->loadActiveAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertNotNull($appServers['serverNameHash1']->getLastFrontendUsage());
    }

    /**
     * Add second new application server in frontend.
     */
    public function testUpdateAppServerInformationAddAppServer(): void
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get('oxUtilsDate')->getTime();

        $this->storeAppServer2Information($currentTime);

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInFrontend();

        /** @var ApplicationServer[] $appServers */
        $appServers = $service->loadAppServerList();

        $this->assertEquals(2, count($appServers));
        $this->assertEquals('serverNameHash1', $appServers['serverNameHash1']->getId());
        $this->assertEquals('serverNameHash2', $appServers['serverNameHash2']->getId());
    }

    /**
     * Add second new application server in frontend, when one server is not active anymore.
     */
    public function testUpdateAppServerInformationIfOneIsNotActiveAppServer(): void
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get('oxUtilsDate')->getTime();

        $this->storeAppServer2Information(($currentTime - (25 * 3600)));

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInFrontend();

        /** @var ApplicationServer[] $appServers */
        $appServers = $service->loadActiveAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertEquals('serverNameHash1', $appServers['serverNameHash1']->getId());
    }

    /**
     * Add second new application server in admin, when the other is out dated and must be deleted.
     */
    public function testUpdateAppServerInformationIfOneIsOutdatedAppServer(): void
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get('oxUtilsDate')->getTime();

        $this->storeAppServer2Information(($currentTime - (75 * 3600)));

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInAdmin();

        /** @var ApplicationServer[] $appServers */
        $appServers = $service->loadAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertEquals('serverNameHash1', $appServers['serverNameHash1']->getId());
    }

    private function getApplicationServerServiceObject($currentTime)
    {
        $config = Registry::getConfig();
        $database = DatabaseProvider::getDb();
        $appServerDao = oxNew(ApplicationServerDao::class, $database, $config);
        $utilsServer = $this->getMockBuilder(UtilsServer::class)
            ->getMock();
        $utilsServer->expects($this->any())
            ->method('getServerNodeId')
            ->willReturn('serverNameHash1');
        $utilsServer->expects($this->any())
            ->method('getServerIp')
            ->willReturn('127.0.0.1');

        return oxNew(ApplicationServerService::class, $appServerDao, $utilsServer, $currentTime);
    }

    private function storeAppServer1Information(int|float $timestamp): void
    {
        $config = Registry::getConfig();
        $appServer = [
            'id' => 'serverNameHash1',
            'timestamp' => $timestamp,
            'ip' => '127.0.0.1',
            'lastFrontendUsage' => '',
            'lastAdminUsage' => 'adminUsageTimestampUpdated',
        ];
        $config->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', $appServer);
    }

    private function storeAppServer2Information($timestamp): void
    {
        $config = Registry::getConfig();
        $appServer = [
            'id' => 'serverNameHash2',
            'timestamp' => $timestamp,
            'ip' => '127.0.0.1',
            'lastFrontendUsage' => '',
            'lastAdminUsage' => 'adminUsageTimestampUpdated',
        ];
        $config->saveSystemConfigParameter('arr', 'aServersData_serverNameHash2', $appServer);
    }
}
