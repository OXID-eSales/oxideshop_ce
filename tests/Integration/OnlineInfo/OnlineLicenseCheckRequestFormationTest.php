<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\OnlineInfo;

/**
 * Class Integration_OnlineInfo_OnlineLicenseCheckRequestFormationTest
 *
 * @covers \OxidEsales\EshopCommunity\Core\OnlineServerEmailBuilder
 * @covers \OxidEsales\EshopCommunity\Core\SimpleXml
 * @covers \OxidEsales\EshopCommunity\Core\OnlineLicenseCheckCaller
 * @covers \OxidEsales\EshopCommunity\Core\UserCounter
 * @covers \OxidEsales\EshopCommunity\Core\OnlineLicenseCheck
 */
class OnlineLicenseCheckRequestFormationTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * imitating package revision file and return shop dir
     *
     * @return string path to virtual shop directory with pkg.rev file
     */
    private function mockPackageRevisionFile()
    {
        $vfsStream = $this->getVfsStreamWrapper();
        $shopDir = "shopdir";
        $vfsStream->createFile($shopDir . DIRECTORY_SEPARATOR . 'pkg.rev', 'somerevisionstring');
        $fakeShopDir = $vfsStream->getRootPath() . $shopDir . DIRECTORY_SEPARATOR;
        return $fakeShopDir;
    }

    /**
     *
     */
    public function testRequestFormationWithExistingSerials()
    {
        $config = $this->getConfig();

        $config->saveShopConfVar('arr', 'aSerials', array('license_key'));
        $config->saveShopConfVar('arr', 'sClusterId', array('generated_unique_cluster_id'));
        $validNodeTime = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()
            ->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");
        $config->saveSystemConfigParameter('arr', 'aServersData_server_id1', array(
            'id' => 'server_id1',
            'timestamp' => $validNodeTime,
            'ip' => '127.0.0.1',
            'lastFrontendUsage' => $validNodeTime,
            'lastAdminUsage' => $validNodeTime
        ));

        // imitating package revision file
        $config->setConfigParam('sShopDir', $this->mockPackageRevisionFile());

        $edition = $config->getEdition();
        $version = $config->getVersion();
        $shopUrl = $config->getShopUrl();
        $revision = $config->getRevision();
        $iAdminUsers = $this->getTestConfig()->getShopEdition() == 'EE' ? 6 : 1;

        $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $xml .= '<olcRequest>';
        $xml .=   '<pVersion>1.1</pVersion>';
        $xml .=   '<keys><key>license_key</key></keys>';
        if ($revision) {
            $xml .= "<revision>$revision</revision>";
        } else {
            $xml .= '<revision></revision>';
        }
        $xml .=   '<productSpecificInformation>';
        $xml .=     '<servers>';
        $xml .=       '<server>';
        $xml .=         '<id>server_id1</id>';
        $xml .=         '<ip>127.0.0.1</ip>';
        $xml .=         "<lastFrontendUsage>$validNodeTime</lastFrontendUsage>";
        $xml .=         "<lastAdminUsage>$validNodeTime</lastAdminUsage>";
        $xml .=       '</server>';
        $xml .=     '</servers>';
        $xml .=     '<counters>';
        $xml .=       '<counter>';
        $xml .=         '<name>admin users</name>';
        $xml .=         "<value>$iAdminUsers</value>";
        $xml .=       '</counter>';
        $xml .=       '<counter>';
        $xml .=         '<name>active admin users</name>';
        $xml .=         "<value>$iAdminUsers</value>";
        $xml .=       '</counter>';
        $xml .=       '<counter>';
        $xml .=         '<name>subShops</name>';
        $xml .=         '<value>1</value>';
        $xml .=       '</counter>';
        $xml .=     '</counters>';
        $xml .=   '</productSpecificInformation>';
        $xml .=   '<clusterId>generated_unique_cluster_id</clusterId>';
        $xml .=   "<edition>$edition</edition>";
        $xml .=   "<version>$version</version>";
        $xml .=   "<shopUrl>$shopUrl</shopUrl>";
        $xml .=   '<productId>eShop</productId>';
        $xml .= '</olcRequest>'."\n";

        $curl = $this->getMockBuilder(\OxidEsales\Eshop\Core\Curl::class)
            ->setMethods(['setParameters', 'execute','getStatusCode'])
            ->getMock();
        $curl->expects($this->atLeastOnce())->method('setParameters')->with($this->equalTo(array('xmlRequest' => $xml)));
        $curl->expects($this->any())->method('execute')->will($this->returnValue(true));
        $curl->expects($this->any())->method('getStatusCode')->will($this->returnValue(200));
        /** @var \OxidEsales\Eshop\Core\Curl $curl */

        $emailBuilder = oxNew(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class);
        $simpleXml = oxNew(\OxidEsales\Eshop\Core\SimpleXml::class);
        $licenseCaller = new \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller($curl, $emailBuilder, $simpleXml);

        $userCounter = oxNew(\OxidEsales\Eshop\Core\UserCounter::class);
        $appServerExporter = $this->getApplicationServerExporter();
        $licenseCheck = new \OxidEsales\Eshop\Core\OnlineLicenseCheck($licenseCaller);
        $licenseCheck->setUserCounter($userCounter);
        $licenseCheck->setAppServerExporter($appServerExporter);

        $licenseCheck->validateShopSerials();
    }

    public function testRequestFormationWithNewSerial()
    {
        $config = $this->getConfig();

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()
            ->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");
        $config->setConfigParam('aSerials', array('license_key'));
        $config->setConfigParam('sClusterId', array('generated_unique_cluster_id'));
        $validNodeTime = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $config->saveSystemConfigParameter('arr', 'aServersData_server_id1', array(
            'id' => 'server_id1',
            'timestamp' => $validNodeTime,
            'ip' => '127.0.0.1',
            'lastFrontendUsage' => $validNodeTime,
            'lastAdminUsage' => $validNodeTime,
            'isValid' => true,
        ));

        // imitating package revision file
        $config->setConfigParam('sShopDir', $this->mockPackageRevisionFile());

        $edition = $config->getEdition();
        $version = $config->getVersion();
        $shopUrl = $config->getShopUrl();
        $revision = $config->getRevision();
        $adminUsers = $this->getTestConfig()->getShopEdition() == 'EE' ? 6 : 1;

        $sXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $sXml .= '<olcRequest>';
        $sXml .=   '<pVersion>1.1</pVersion>';
        $sXml .=   '<keys>';
        $sXml .=   '<key>license_key</key>';
        $sXml .=   '<key state="new">new_serial</key>';
        $sXml .=   '</keys>';
        if ($revision) {
            $sXml .= "<revision>$revision</revision>";
        } else {
            $sXml .= '<revision></revision>';
        }
        $sXml .=   '<productSpecificInformation>';
        $sXml .=     '<servers>';
        $sXml .=       '<server>';
        $sXml .=         '<id>server_id1</id>';
        $sXml .=         '<ip>127.0.0.1</ip>';
        $sXml .=         "<lastFrontendUsage>$validNodeTime</lastFrontendUsage>";
        $sXml .=         "<lastAdminUsage>$validNodeTime</lastAdminUsage>";
        $sXml .=       '</server>';
        $sXml .=     '</servers>';
        $sXml .=     '<counters>';
        $sXml .=       '<counter>';
        $sXml .=         '<name>admin users</name>';
        $sXml .=         "<value>$adminUsers</value>";
        $sXml .=       '</counter>';
        $sXml .=       '<counter>';
        $sXml .=         '<name>active admin users</name>';
        $sXml .=         "<value>$adminUsers</value>";
        $sXml .=       '</counter>';
        $sXml .=       '<counter>';
        $sXml .=         '<name>subShops</name>';
        $sXml .=         '<value>1</value>';
        $sXml .=       '</counter>';
        $sXml .=     '</counters>';
        $sXml .=   '</productSpecificInformation>';
        $sXml .=   '<clusterId>generated_unique_cluster_id</clusterId>';
        $sXml .=   "<edition>$edition</edition>";
        $sXml .=   "<version>$version</version>";
        $sXml .=   "<shopUrl>$shopUrl</shopUrl>";
        $sXml .=   '<productId>eShop</productId>';
        $sXml .= '</olcRequest>'."\n";

        $curl = $this->getMockBuilder(\OxidEsales\Eshop\Core\Curl::class)
            ->setMethods(['setParameters', 'execute','getStatusCode'])
            ->getMock();
        $curl->expects($this->any())->method('execute')->will($this->returnValue(true));
        $curl->expects($this->any())->method('getStatusCode')->will($this->returnValue(200));
        $curl->expects($this->atLeastOnce())->method('setParameters')->with($this->equalTo(array('xmlRequest' => $sXml)));
        /** @var \OxidEsales\Eshop\Core\Curl $curl */

        $emailBuilder = oxNew(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class);

        $simpleXml = oxNew(\OxidEsales\Eshop\Core\SimpleXml::class);
        $licenseCheckCaller = new \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller($curl, $emailBuilder, $simpleXml);

        $userCounter = oxNew(\OxidEsales\Eshop\Core\UserCounter::class);
        $appServerExporter = $this->getApplicationServerExporter();

        $licenseCheck = new \OxidEsales\Eshop\Core\OnlineLicenseCheck($licenseCheckCaller);
        $licenseCheck->setUserCounter($userCounter);
        $licenseCheck->setAppServerExporter($appServerExporter);

        $licenseCheck->validateNewSerial('new_serial');
    }

    /**
     * @return \OxidEsales\Eshop\Core\Service\ApplicationServerExporterInterface
     */
    private function getApplicationServerExporter()
    {
        $config = $this->getConfig();
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $database, $config);
        $utilsServer = oxNew(\OxidEsales\Eshop\Core\UtilsServer::class);
        $service = oxNew(
            \OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
            $appServerDao,
            $utilsServer,
            \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime()
        );

        $exporter = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerExporter::class, $service);

        return $exporter;
    }
}
