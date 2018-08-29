<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\OnlineInfo;

/**
 * Class Integration_OnlineInfo_FrontendServersInformationStoringTest
 *
 * @covers \OxidEsales\Eshop\Core\Service\ApplicationServerService
 * @covers \OxidEsales\Eshop\Core\DataObject\ApplicationServer
 */
class FrontendServersInformationStoringTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");
    }

    /**
     * Add first new application server in frontend.
     */
    public function testUpdateAppServerInformationNewAppServer()
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInFrontend();

        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer[] $appServers */
        $appServers = $service->loadAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertEquals('serverNameHash1', $appServers['serverNameHash1']->getId());
    }

    /**
     * There is one up to date application server, so no data to update.
     */
    public function testUpdateAppServerInformationAppServerExists()
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();

        $this->storeAppServer1Information(($currentTime - (11 * 3600)));

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInFrontend();

        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer[] $appServers */
        $appServers = $service->loadAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertEquals('adminUsageTimestampUpdated', $appServers['serverNameHash1']->getLastAdminUsage());
    }

    /**
     * There is one not active application server, the information of this server must be updated.
     */
    public function testUpdateAppServerInformationUpdateAppServerData()
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();

        $this->storeAppServer1Information(($currentTime - (25 * 3600)));

        $service = $this->getApplicationServerServiceObject($currentTime);
        $appServers = $service->loadActiveAppServerList();

        $this->assertEquals(0, count($appServers));

        $service->updateAppServerInformationInFrontend();

        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer[] $appServers */
        $appServers = $service->loadActiveAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertNotNull($appServers['serverNameHash1']->getLastFrontendUsage());
    }

    /**
     * Add second new application server in frontend.
     */
    public function testUpdateAppServerInformationAddAppServer()
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();

        $this->storeAppServer2Information($currentTime);

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInFrontend();

        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer[] $appServers */
        $appServers = $service->loadAppServerList();

        $this->assertEquals(2, count($appServers));
        $this->assertEquals('serverNameHash1', $appServers['serverNameHash1']->getId());
        $this->assertEquals('serverNameHash2', $appServers['serverNameHash2']->getId());
    }

    /**
     * Add second new application server in frontend, when one server is not active anymore.
     */
    public function testUpdateAppServerInformationIfOneIsNotActiveAppServer()
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();

        $this->storeAppServer2Information(($currentTime - (25 * 3600)));

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInFrontend();

        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer[] $appServers */
        $appServers = $service->loadActiveAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertEquals('serverNameHash1', $appServers['serverNameHash1']->getId());
    }

    /**
     * Add second new application server in admin, when the other is out dated and must be deleted.
     */
    public function testUpdateAppServerInformationIfOneIsOutdatedAppServer()
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();

        $this->storeAppServer2Information(($currentTime - (75 * 3600)));

        $service = $this->getApplicationServerServiceObject($currentTime);
        $service->updateAppServerInformationInAdmin();

        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer[] $appServers */
        $appServers = $service->loadAppServerList();

        $this->assertEquals(1, count($appServers));
        $this->assertEquals('serverNameHash1', $appServers['serverNameHash1']->getId());
    }

    private function getApplicationServerServiceObject($currentTime)
    {
        $config = $this->getConfig();
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $database, $config);
        $utilsServer = $this->getMockBuilder(\OxidEsales\Eshop\Core\UtilsServer::class)
            ->setMethods(['getServerNodeId', 'getServerIp'])
            ->getMock();
        $utilsServer->expects($this->any())->method('getServerNodeId')->will($this->returnValue('serverNameHash1'));
        $utilsServer->expects($this->any())->method('getServerIp')->will($this->returnValue('127.0.0.1'));

        return oxNew(
            \OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
            $appServerDao,
            $utilsServer,
            $currentTime
        );
    }

    private function storeAppServer1Information($timestamp)
    {
        $config = $this->getConfig();
        $appServer = array(
            'id'                => 'serverNameHash1',
            'timestamp'         => $timestamp,
            'ip'                => '127.0.0.1',
            'lastFrontendUsage' => '',
            'lastAdminUsage'    => 'adminUsageTimestampUpdated'
        );
        $config->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', $appServer);
    }

    private function storeAppServer2Information($timestamp)
    {
        $config = $this->getConfig();
        $appServer = array(
            'id'                => 'serverNameHash2',
            'timestamp'         => $timestamp,
            'ip'                => '127.0.0.1',
            'lastFrontendUsage' => '',
            'lastAdminUsage'    => 'adminUsageTimestampUpdated'
        );
        $config->saveSystemConfigParameter('arr', 'aServersData_serverNameHash2', $appServer);
    }
}
