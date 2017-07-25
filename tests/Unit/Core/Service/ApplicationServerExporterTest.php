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
        $facade = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerExporter::class, $service);

        $appServers = $facade->exportAppServerList();

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
        $server->setLastAdminUsage('adminUsageTimestamp');
        $server->setIsValid();

        $activeServers = array($server);
        $activeServers2 = array($server, $server);

        $expectedServerCollection = array(
            'id'                => 'serverNameHash1',
            'ip'                => '127.0.0.1',
            'lastFrontendUsage' => 'frontendUsageTimestamp',
            'lastAdminUsage'    => 'adminUsageTimestamp'
        );

        return [
            [null, 0, null],
            [1, 0, null],
            [0, 0, null],
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
        $config = Registry::getConfig();
        $databaseProvider = oxNew(DatabaseProvider::class);
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        /** @var \OxidEsales\Eshop\Core\UtilsServer $utilsServer */
        $utilsServer = oxNew(\OxidEsales\Eshop\Core\UtilsServer::class);
        $service = $this->getMock(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
            array("loadActiveAppServerList"),
            array($appServerDao, $utilsServer, \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime()));
        $service->expects($this->any())->method('loadActiveAppServerList')->will($this->returnValue($appServerList));

        return $service;
    }
}
