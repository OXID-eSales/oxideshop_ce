<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \stdClass;

class OnlineLicenseCheckTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function testRequestFormation()
    {
        $adminUsers = 25;
        $activeAdminUsers = 10;
        $subShops = 5;
        $servers = array('7da43ed884a1ad1d6035d4c1d630fc4e' => array(
            'id' => '7da43ed884a1ad1d6035d4c1d630fc4e',
            'timestamp' => '1409911182',
            'ip' => null,
            'lastFrontendUsage' => '1409911182',
            'lastAdminUsage' => null,
        ));
        $counters = array(
            array(
                'name' => 'admin users',
                'value' => $adminUsers,
            ),
            array(
                'name' => 'active admin users',
                'value' => $activeAdminUsers,
            ),
            array(
                'name' => 'subShops',
                'value' => $subShops,
            )
        );

        $config = $this->getConfigMock($subShops);

        /** @var \OxidEsales\Eshop\Core\Config $config */
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $config);

        $request = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckRequest::class);
        $request->revision = $this->getConfig()->getRevision();
        $request->pVersion = '1.1';
        $request->productId = 'eShop';
        $request->keys = array('key' => array('validSerial'));
        $request->productSpecificInformation = new stdClass();
        $request->productSpecificInformation->servers = array('server' => $servers);
        $request->productSpecificInformation->counters = array('counter' => $counters);

        $caller = $this->getMockBuilder('\OxidEsales\Eshop\Core\OnlineLicenseCheckCaller')
            ->disableOriginalConstructor()
            ->setMethods(['doRequest'])
            ->getMock();
        $caller->expects($this->once())->method('doRequest')->with($request);

        /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller $caller */

        $userCounter = $this->getMockBuilder('\OxidEsales\Eshop\Core\UserCounter')
            ->setMethods(['getAdminCount', 'getActiveAdminCount'])
            ->getMock();
        $userCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue($adminUsers));
        $userCounter->expects($this->once())->method('getActiveAdminCount')->will($this->returnValue($activeAdminUsers));
        /** @var \OxidEsales\Eshop\Core\UserCounter $userCounter */

        $appServerExporter = $this->getApplicationServerExporterMock($servers);

        $licenseCheck = new \OxidEsales\Eshop\Core\OnlineLicenseCheck($caller);
        $licenseCheck->setAppServerExporter($appServerExporter);
        $licenseCheck->setUserCounter($userCounter);

        $licenseCheck->validate('validSerial');
    }

    /**
     * Test successful license key validation.
     */
    public function testValidationPassed()
    {
        $response = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckResponse::class);
        $response->code = 0;
        $response->message = 'ACK';

        $caller = $this->getOnlineLicenseCheckCallerMock($response);

        $licenseCheck = new \OxidEsales\Eshop\Core\OnlineLicenseCheck($caller);

        $this->assertEquals(true, $licenseCheck->validate('validSerial'));

        return $licenseCheck;
    }

    /**
     * @depends testValidationPassed
     *
     * @param \OxidEsales\Eshop\Core\OnlineLicenseCheck $licenseCheck
     */
    public function testErrorMessageEmptyOnSuccess($licenseCheck)
    {
        $this->assertEquals('', $licenseCheck->getErrorMessage());
    }

    /**
     * Test failed license key validation.
     */
    public function testValidationFailed()
    {
        $response = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckResponse::class);
        $response->code = 1;
        $response->message = 'NACK';

        $caller = $this->getOnlineLicenseCheckCallerMock($response);
        /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller $caller */

        $licenseCheck = new \OxidEsales\Eshop\Core\OnlineLicenseCheck($caller);

        $this->assertEquals(false, $licenseCheck->validate('invalidSerial'));

        return $licenseCheck;
    }

    /**
     * @depends testValidationFailed
     *
     * @param \OxidEsales\Eshop\Core\OnlineLicenseCheck $licenseCheck
     */
    public function testErrorMessageSetOnFailure($licenseCheck)
    {
        $this->assertEquals(
            \OxidEsales\Eshop\Core\Registry::getLang()->translateString('OLC_ERROR_SERIAL_NOT_VALID'),
            $licenseCheck->getErrorMessage()
        );
    }

    public function testSerialsAreTakenFromConfigInShopSerialsValidation()
    {
        $adminUsers = 25;
        $activeAdminUsers = 10;
        $subShops = 5;
        $servers = array('7da43ed884a1ad1d6035d4c1d630fc4e' => array(
            'id' => '7da43ed884a1ad1d6035d4c1d630fc4e',
            'timestamp' => '1409911182',
            'ip' => null,
            'lastFrontendUsage' => '1409911182',
            'lastAdminUsage' => null,
        ));
        $counters = array(
            array(
                'name' => 'admin users',
                'value' => $adminUsers,
            ),
            array(
                'name' => 'active admin users',
                'value' => $activeAdminUsers,
            ),
            array(
                'name' => 'subShops',
                'value' => $subShops,
            )
        );

        $config = $this->getConfigMock($subShops);

        /** @var \OxidEsales\Eshop\Core\Config $config */
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $config);

        $request = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckRequest::class);
        $request->edition = $this->getConfig()->getEdition();
        $request->version = $this->getConfig()->getVersion();
        $request->revision = $this->getConfig()->getRevision();
        $request->shopUrl = $this->getConfig()->getShopUrl();
        $request->pVersion = '1.1';
        $request->productId = 'eShop';
        $request->keys = array('key' => array('key1', 'key2'));

        $request->productSpecificInformation = new stdClass();
        $request->productSpecificInformation->servers = array('server' => $servers);
        $request->productSpecificInformation->counters = array('counter' => $counters);

        $this->getConfig()->setConfigParam("aSerials", array('key1', 'key2'));

        $caller = $this->getMockBuilder('\OxidEsales\Eshop\Core\OnlineLicenseCheckCaller')
            ->disableOriginalConstructor()
            ->setMethods(['doRequest'])
            ->getMock();
        $caller->expects($this->once())->method('doRequest')->with($request);
        /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller $caller */

        $userCounter = $this->getMockBuilder('\OxidEsales\Eshop\Core\UserCounter')
            ->setMethods(['getAdminCount', 'getActiveAdminCount'])
            ->getMock();
        $userCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue($adminUsers));
        $userCounter->expects($this->once())->method('getActiveAdminCount')->will($this->returnValue($activeAdminUsers));
        /** @var \OxidEsales\Eshop\Core\UserCounter $userCounter */

        $appServerExporter = $this->getApplicationServerExporterMock($servers);

        $licenseCheck = new \OxidEsales\Eshop\Core\OnlineLicenseCheck($caller);
        $licenseCheck->setAppServerExporter($appServerExporter);
        $licenseCheck->setUserCounter($userCounter);
        $licenseCheck->validateShopSerials();
    }

    public function testNewSerialIsAddedToExistingSerials()
    {
        $subShops = 5;
        $counters = array(array(
            'name' => 'subShops',
            'value' => $subShops,
        ));

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");

        $config = $this->getConfigMock($subShops);

        /** @var \OxidEsales\Eshop\Core\Config $config */
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $config);

        $request = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckRequest::class);
        $request->edition = $this->getConfig()->getEdition();
        $request->version = $this->getConfig()->getVersion();
        $request->revision = $this->getConfig()->getRevision();
        $request->shopUrl = $this->getConfig()->getShopUrl();
        $request->pVersion = '1.1';
        $request->productId = 'eShop';
        $keys = array('key1', 'key2', array('attributes' => array('state' => 'new'), 'value' => 'new_serial'));
        $request->keys = array('key' => $keys);

        $request->productSpecificInformation = new stdClass();
        $request->productSpecificInformation->counters = array('counter' => $counters);

        $this->getConfig()->setConfigParam("aSerials", array('key1', 'key2'));

        $caller = $this->getMockBuilder('\OxidEsales\Eshop\Core\OnlineLicenseCheckCaller')
            ->disableOriginalConstructor()
            ->setMethods(['doRequest'])
            ->getMock();
        $caller->expects($this->once())->method('doRequest')->with($request);
        /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller $caller */

        $licenseCheck = new \OxidEsales\Eshop\Core\OnlineLicenseCheck($caller);
        $licenseCheck->validateNewSerial('new_serial');
    }

    public function testIsExceptionWhenExceptionWasThrown()
    {
        $exception = new \OxidEsales\Eshop\Core\Exception\StandardException();
        $caller = $this->getMockBuilder('\OxidEsales\Eshop\Core\OnlineLicenseCheckCaller')
            ->disableOriginalConstructor()
            ->setMethods(['doRequest'])
            ->getMock();
        $caller->expects($this->any())->method('doRequest')->will($this->throwException($exception));
        /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller $caller */

        $licenseCheck = new \OxidEsales\Eshop\Core\OnlineLicenseCheck($caller);
        $licenseCheck->validate('validSerial');

        $this->assertEquals(true, $licenseCheck->isException());
    }

    public function testLog()
    {
        $response = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckResponse::class);
        $response->code = 0;
        $response->message = 'ACK';

        $caller = $this->getOnlineLicenseCheckCallerMock($response);
        /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller $caller */

        $licenseCheck = new \OxidEsales\Eshop\Core\OnlineLicenseCheck($caller);

        $this->setTime(10);

        $licenseCheck->validate('validSerial');

        $this->assertEquals(10, $this->getConfig()->getConfigParam('iOlcSuccess'));
    }

    /**
     * @param array $appServerList An array of application servers to return.
     *
     * @return \OxidEsales\Eshop\Core\Service\ApplicationServerExporter
     */
    private function getApplicationServerExporterMock($appServerList)
    {
        $appServer = $this->getMockBuilder('\OxidEsales\Eshop\Core\Service\ApplicationServerServiceInterface')->getMock();

        $exporter = $this->getMockBuilder('\OxidEsales\Eshop\Core\Service\ApplicationServerExporter')
            ->setConstructorArgs([$appServer])
            ->getMock();
        $exporter->expects($this->once())->method('exportAppServerList')->willReturn($appServerList);

        return $exporter;
    }

    /**
     * @param array $subShops
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    private function getConfigMock($subShops)
    {
        $config = $this->getMockBuilder('\OxidEsales\Eshop\Core\Config')
            ->setMethods(['getMandateCount'])
            ->getMock();
        $config->expects($this->any())->method('getMandateCount')->will($this->returnValue($subShops));

        return $config;
    }

    /**
     * @param array $response
     *
     * @return \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller
     */
    private function getOnlineLicenseCheckCallerMock($response)
    {
        $caller = $this->getMockBuilder('\OxidEsales\Eshop\Core\OnlineLicenseCheckCaller')
            ->disableOriginalConstructor()
            ->setMethods(['doRequest'])
            ->getMock();
        $caller->expects($this->any())->method('doRequest')->will($this->returnValue($response));
        return $caller;
    }
}
