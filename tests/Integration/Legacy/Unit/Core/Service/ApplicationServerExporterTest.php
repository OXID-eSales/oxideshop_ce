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
class ApplicationServerExporterTest extends \PHPUnit\Framework\TestCase
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
    public function dataProviderForExportApplicationServerList(): \Iterator
    {
        $server = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $server->setId('serverNameHash1');
        $server->setTimestamp('createdTimestamp');
        $server->setIp('127.0.0.1');
        $server->setLastFrontendUsage('frontendUsageTimestamp');

        $activeServers = [$server];
        $activeServers2 = [$server, $server];

        $expectedServerCollection = ['id'                => 'serverNameHash1', 'ip'                => '127.0.0.1', 'lastFrontendUsage' => 'frontendUsageTimestamp', 'lastAdminUsage'    => ''];
        yield [false, 0, null];
        yield [[], 0, null];
        yield [$activeServers, 1, $expectedServerCollection];
        yield [$activeServers2, 2, $expectedServerCollection];
    }

    /**
     * @param array $appServerList An array of application servers to return.
     *
     * @return \OxidEsales\Eshop\Core\Service\ApplicationServerService
     */
    private function getApplicationServerServiceMock($appServerList)
    {
        $appServer = $this->getMockBuilder(\OxidEsales\Eshop\Core\Service\ApplicationServerServiceInterface::class)->getMock();
        $appServer->method('loadActiveAppServerList')->willReturn($appServerList);

        return $appServer;
    }
}
