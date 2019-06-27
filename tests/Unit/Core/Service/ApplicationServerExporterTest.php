<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * @covers \OxidEsales\Eshop\Core\Service\ApplicationServerExporter
 */
class ApplicationServerExporterTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * @param array $activeServers            An array of application servers.
     * @param int   $count                    Expected count of application servers.
     * @param array $expectedServerCollection Expected output.
     *
     * @dataProvider dataProviderForExportApplicationServerList
     */
    public function testExport($activeServers, $count, $expectedServerCollection)
    {
        $service = $this->getApplicationServerServiceMock($activeServers);
        $exporter = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerExporter::class, $service);

        $appServers = $exporter->exportAppServerList();

        $this->assertCount($count, $appServers);

        $this->assertEquals($expectedServerCollection, $appServers[0]);
    }

    /**
     * Data provider for the test method testExport.
     */
    public function dataProviderForExportApplicationServerList()
    {
        $server = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $server->setId('serverNameHash1');
        $server->setTimestamp('createdTimestamp');
        $server->setIp('127.0.0.1');
        $server->setLastFrontendUsage('frontendUsageTimestamp');

        $activeServers = array($server);
        $activeServers2 = array($server, $server);

        $expectedServerCollection = array(
            'id'                => 'serverNameHash1',
            'ip'                => '127.0.0.1',
            'lastFrontendUsage' => 'frontendUsageTimestamp',
            'lastAdminUsage'    => ''
        );

        return [
            [false, 0, null],
            [[], 0, null],
            [$activeServers, 1, $expectedServerCollection],
            [$activeServers2, 2, $expectedServerCollection],
        ];
    }

    /**
     * @param array $appServerList An array of application servers to return.
     *
     * @return \OxidEsales\Eshop\Core\Service\ApplicationServerService
     */
    private function getApplicationServerServiceMock($appServerList)
    {
        $appServer = $this->getMockBuilder('\OxidEsales\Eshop\Core\Service\ApplicationServerServiceInterface')->getMock();
        $appServer->expects($this->any())->method('loadActiveAppServerList')->will($this->returnValue($appServerList));

        return $appServer;
    }
}
