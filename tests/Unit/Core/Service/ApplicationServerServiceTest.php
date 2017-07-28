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

/**
 * @covers \OxidEsales\Eshop\Core\Service\ApplicationServerService
 */
class ApplicationServerServiceTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");
    }

    public function testLoadAppServerList()
    {
        $appServerDao = $this->getApplicationServerDaoMock("findAll", ['foundAppServer']);

        $service = $this->getApplicationServerService($appServerDao);

        $this->assertEquals(array('foundAppServer'), $service->loadAppServerList());
    }

    public function testDeleteAppServer()
    {
        $id = 'testId';

        $appServerDao = $this->getApplicationServerDaoMock("delete", $id);

        $service = $this->getApplicationServerService($appServerDao);

        $this->assertEquals($id, $service->deleteAppServerById($id));
    }

    public function testLoadAppServer()
    {
        $id = 'testId';

        $appServerDao = $this->getApplicationServerDaoMock("findById", $id);

        $service = $this->getApplicationServerService($appServerDao);

        $this->assertEquals($id, $service->loadAppServer($id));
    }

    public function testLoadAppServerDoesNotExists()
    {
        $this->setExpectedException(\OxidEsales\Eshop\Core\Exception\NoResultException::class);
        $id = 'testId';

        $appServerDao = $this->getApplicationServerDaoMock("findById", null);

        $service = $this->getApplicationServerService($appServerDao);

        $service->loadAppServer($id);
    }

    public function testSaveAppServerIfExists()
    {
        $id = 'testId';

        $appServerDao = $this->getMockBuilder(\OxidEsales\Eshop\Core\Dao\BaseDaoInterface::class)
            ->getMock();
        $appServerDao->expects($this->once())->method('findById')->will($this->returnValue($id));
        $appServerDao->expects($this->once())->method('update')->will($this->returnValue($id));

        $server = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $server->setId($id);

        $service = $this->getApplicationServerService($appServerDao);

        $this->assertEquals($id, $service->saveAppServer($server));
    }

    public function testSaveAppServerNewElement()
    {
        $id = 'testId';

        $appServerDao = $this->getMockBuilder(\OxidEsales\Eshop\Core\Dao\BaseDaoInterface::class)
            ->getMock();
        $appServerDao->expects($this->once())->method('findById')->will($this->returnValue(null));
        $appServerDao->expects($this->once())->method('insert')->will($this->returnValue($id));

        $server = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $server->setId($id);

        $service = $this->getApplicationServerService($appServerDao);

        $this->assertEquals($id, $service->saveAppServer($server));
    }

    public function testLoadActiveAppServerListIfServerIsValid()
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();

        $server = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $server->setId('serverNameHash1');
        $server->setTimestamp($currentTime - (11 * 3600));
        $server->setIp('127.0.0.1');
        $server->setLastAdminUsage('adminUsageTimestamp');

        $appServerDao = $this->getApplicationServerDaoMock("findAll", [$server]);

        $service = $this->getApplicationServerService($appServerDao);

        $this->assertEquals(['serverNameHash1' => $server], $service->loadActiveAppServerList());
    }

    public function testLoadActiveAppServerListIfServerIsNotValid()
    {
        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();

        $server = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $server->setId('serverNameHash1');
        $server->setTimestamp($currentTime - (25 * 3600));
        $server->setIp('127.0.0.1');
        $server->setLastAdminUsage('adminUsageTimestamp');

        $appServerDao = $this->getApplicationServerDaoMock("findAll", [$server]);

        $service = $this->getApplicationServerService($appServerDao);

        $this->assertEquals([], $service->loadActiveAppServerList());
    }

    public function testLoadActiveAppServerListIfNoServersFound()
    {
        $appServerDao = $this->getApplicationServerDaoMock("findAll", []);

        $service = $this->getApplicationServerService($appServerDao);

        $this->assertEquals([], $service->loadActiveAppServerList());
    }

    public function testUpdateAppServerInformationNewAppServer()
    {
        $id = 'testId';

        $appServerDao = $this->getMockBuilder(\OxidEsales\Eshop\Core\Dao\BaseDaoInterface::class)
            ->getMock();
        $appServerDao->expects($this->once())->method('findById')->will($this->returnValue(null));
        $appServerDao->expects($this->once())->method('findAll')->will($this->returnValue([]));
        $appServerDao->expects($this->once())->method('insert')->will($this->returnValue($id));

        $utilsServer = $this->getMockBuilder(\OxidEsales\Eshop\Core\UtilsServer::class)
            ->setMethods(['getServerNodeId', 'getServerIp'])
            ->getMock();
        $utilsServer->expects($this->any())->method('getServerNodeId')->will($this->returnValue('serverNameHash2'));
        $utilsServer->expects($this->any())->method('getServerIp')->will($this->returnValue('127.0.0.1'));

        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();
        $service = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class, $appServerDao, $utilsServer, $currentTime);
        $service->updateAppServerInformation();
    }

    private function getApplicationServerDaoMock($methodToMock, $expectedReturnValue)
    {
        $appServerDao = $this->getMockBuilder(\OxidEsales\Eshop\Core\Dao\BaseDaoInterface::class)
            ->getMock();
        $appServerDao->expects($this->once())->method($methodToMock)->will($this->returnValue($expectedReturnValue));

        return $appServerDao;
    }

    private function getApplicationServerService($appServerDao)
    {
        $utilsServer = oxNew(\OxidEsales\Eshop\Core\UtilsServer::class);
        $currentTime = \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime();

        return oxNew(
            \OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
            $appServerDao,
            $utilsServer,
            $currentTime
        );
    }
}
