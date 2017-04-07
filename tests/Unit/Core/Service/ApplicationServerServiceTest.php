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
 * @covers \OxidEsales\Eshop\Core\Service\ApplicationServerService
 */
class ApplicationServerServiceTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function testLoadAppServerList()
    {
        $appServerDao = $this->getApplicationServerDaoMock("findAll", array('foundAppServer'));

        $service = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,$appServerDao);

        $this->assertEquals(array('foundAppServer'), $service->loadAppServerList());
    }

    public function testDeleteAppServer()
    {
        $id = 'testId';

        $appServerDao = $this->getApplicationServerDaoMock("delete", $id);

        $service = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,$appServerDao);

        $this->assertEquals($id, $service->deleteAppServerById($id));
    }

    public function testLoadAppServer()
    {
        $id = 'testId';

        $appServerDao = $this->getApplicationServerDaoMock("findById", $id);

        $service = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,$appServerDao);

        $this->assertEquals($id, $service->loadAppServer($id));
    }

    public function testSaveAppServerIfExists()
    {
        $id = 'testId';
        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = $this->getMock(
            \OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class,
            array("findById", "update"),
            array($databaseProvider, $config));
        $appServerDao->expects($this->once())->method('findById')->will($this->returnValue($id));
        $appServerDao->expects($this->once())->method('update')->will($this->returnValue($id));

        $server = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
        $server->setId('testId');

        $service = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,$appServerDao);

        $this->assertEquals($id, $service->saveAppServer($server));
    }

    public function testSaveAppServerNewElement()
    {
        $id = 'testId';
        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = $this->getMock(
            \OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class,
            array("findById", "insert"),
            array($databaseProvider, $config));
        $appServerDao->expects($this->once())->method('findById')->will($this->returnValue(false));
        $appServerDao->expects($this->once())->method('insert')->will($this->returnValue($id));

        $server = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
        $server->setId('testId');

        $service = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,$appServerDao);

        $this->assertEquals($id, $service->saveAppServer($server));
    }

    public function testLoadActiveAppServerListIfServerIsValid()
    {
        $server = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
        $server->setId('serverNameHash1');
        $server->setTimestamp('timestamp');
        $server->setIp('127.0.0.1');
        $server->setLastFrontendUsage('frontendUsageTimestamp');
        $server->setLastAdminUsage('adminUsageTimestamp');
        $server->setIsValid();

        $appServerDao = $this->getApplicationServerDaoMock("findAll", array($server));

        $service = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class, $appServerDao);

        $this->assertEquals(array($server), $service->loadActiveAppServerList());
    }

    public function testLoadActiveAppServerListIfServerIsNotValid()
    {
        $server = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
        $server->setId('serverNameHash1');
        $server->setTimestamp('timestamp');
        $server->setIp('127.0.0.1');
        $server->setLastFrontendUsage('frontendUsageTimestamp');
        $server->setLastAdminUsage('adminUsageTimestamp');
        $server->setIsValid(false);

        $appServerDao = $this->getApplicationServerDaoMock("findAll", array($server));

        $service = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class, $appServerDao);

        $this->assertEquals(array(), $service->loadActiveAppServerList());
    }

    public function testLoadActiveAppServerListIfNoServersFound()
    {
        $appServerDao = $this->getApplicationServerDaoMock("findAll", array());

        $service = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class, $appServerDao);

        $this->assertEquals(array(), $service->loadActiveAppServerList());
    }


    /* public function testGetActiveAppServerListMarkingNotValidNodes()
     {
         $iCurrentTime = 1400000000;
         $this->setTime($iCurrentTime);

         $server = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
         $server->setId('serverNameHash1');
         $server->setTimestamp($iCurrentTime - (11 * 3600));
         $server->setIp('127.0.0.1');
         $server->setLastFrontendUsage('frontendUsageTimestamp');
         $server->setLastAdminUsage('adminUsageTimestamp');
         $server->setIsValid();

         $server2 = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
         $server2->setId('serverNameHash2');
         $server2->setTimestamp($iCurrentTime - (25 * 3600));
         $server2->setIp('127.0.0.1');
         $server2->setLastFrontendUsage('frontendUsageTimestamp');
         $server2->setLastAdminUsage('adminUsageTimestamp');
         $server2->setIsValid();

         $config = Registry::getConfig();
         $service = $this->getMock(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
             array("loadAppServerList"),
             array($config));
         $service->expects($this->any())->method('loadAppServerList')->will($this->returnValue(array($server, $server2)));

         $appServerList = $service->getActiveAppServerList();
         $this->assertEquals(1, count($appServerList));
     }

     public function testGetActiveAppServerList()
     {
         $iCurrentTime = 1400000000;
         $this->setTime($iCurrentTime);

         $server = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
         $server->setId('serverNameHash1');
         $server->setTimestamp($iCurrentTime - (11 * 3600));
         $server->setIp('127.0.0.1');
         $server->setLastFrontendUsage('frontendUsageTimestamp');
         $server->setLastAdminUsage('adminUsageTimestamp');
         $server->setIsValid();

         $server2 = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
         $server2->setId('serverNameHash2');
         $server2->setTimestamp($iCurrentTime - (11 * 3600));
         $server2->setIp('127.0.0.1');
         $server2->setLastFrontendUsage('frontendUsageTimestamp');
         $server2->setLastAdminUsage('adminUsageTimestamp');
         $server2->setIsValid();

         $config = Registry::getConfig();
         $service = $this->getMock(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
             array("loadAppServerList"),
             array($config));
         $service->expects($this->any())->method('loadAppServerList')->will($this->returnValue(array($server, $server2)));

         $appServerList = $service->getActiveAppServerList();
         $this->assertEquals(2, count($appServerList));
     }

     public function testGetActiveAppServerListDeletingNotValidNodes()
     {
         $iCurrentTime = 1400000000;
         $this->setTime($iCurrentTime);

         $appServerDao = $this->getApplicationServerDaoMock("delete", true);

         $server = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
         $server->setId('serverNameHash1');
         $server->setTimestamp($iCurrentTime - (11 * 3600));
         $server->setIp('127.0.0.1');
         $server->setLastFrontendUsage('frontendUsageTimestamp');
         $server->setLastAdminUsage('adminUsageTimestamp');
         $server->setIsValid();

         $server2 = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
         $server2->setId('serverNameHash2');
         $server2->setTimestamp($iCurrentTime - (73 * 3600));
         $server2->setIp('127.0.0.1');
         $server2->setLastFrontendUsage('frontendUsageTimestamp');
         $server2->setLastAdminUsage('adminUsageTimestamp');
         $server2->setIsValid(false);

         $config = Registry::getConfig();
         $service = $this->getMock(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
             array("loadAppServerList","getAppServerDao"),
             array($config));
         $service->expects($this->any())->method('loadAppServerList')->will($this->returnValue(array($server, $server2)));
         $service->expects($this->any())->method('getAppServerDao')->will($this->returnValue($appServerDao));

         $appServerList = $service->getActiveAppServerList();
         $this->assertEquals(1, count($appServerList));
     }
 */
    private function getApplicationServerDaoMock($methodToMock, $expectedReturnValue)
    {
        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = $this->getMock(
            \OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class,
            array($methodToMock),
            array($databaseProvider, $config));
        $appServerDao->expects($this->once())->method($methodToMock)->will($this->returnValue($expectedReturnValue));

        return $appServerDao;
    }
}
