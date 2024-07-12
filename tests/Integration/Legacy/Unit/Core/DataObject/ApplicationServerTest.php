<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\DataObject;

/**
 * @covers \OxidEsales\Eshop\Core\DataObject\ApplicationServer
 */
class ApplicationServerTest extends \PHPUnit\Framework\TestCase
{
    public function testSetGetId()
    {
        $serverNode = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $serverNode->setId('ThisIsServerId');
        $this->assertSame('ThisIsServerId', $serverNode->getId());
    }

    public function testSetGetIp()
    {
        $serverNode = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $serverNode->setIp('11.11.11.11');
        $this->assertSame('11.11.11.11', $serverNode->getIp());
    }

    public function testSetGetTimeStamp()
    {
        $serverNode = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $serverNode->setTimestamp(123456789);
        $this->assertSame(123456789, $serverNode->getTimestamp());
    }

    public function testSetGetLastFrontendUsage()
    {
        $serverNode = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $serverNode->setLastFrontendUsage(123456789);
        $this->assertSame(123456789, $serverNode->getLastFrontendUsage());
    }

    public function testSetGetLastAdminUsage()
    {
        $serverNode = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $serverNode->setLastAdminUsage(123456789);
        $this->assertSame(123456789, $serverNode->getLastAdminUsage());
    }

    /**
     * @dataProvider dataProviderServerIsInUse
     *
     * @param int  $currentTime    The current timestamp.
     * @param int  $serverTime     The server timestamp.
     * @param bool $expectedResult Expected result
     */
    public function testServerIsInUse($currentTime, $serverTime, $expectedResult)
    {
        $serverNode = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $serverNode->setTimestamp($serverTime);
        $this->assertSame($expectedResult, $serverNode->isInUse($currentTime));
    }

    /**
     * Data provider for the test method .
     */
    public function dataProviderServerIsInUse(): \Iterator
    {
        $currentTime = 1400000000;
        yield [null, $currentTime - (25 * 3600), true];
        // If server timestamp is not set at all.
        yield [1, $currentTime - (25 * 3600), true];
        // If server timestamp is not valid.
        yield [$currentTime, $currentTime - (25 * 3600), false];
        // If server TTL has exceeded.
        yield [$currentTime, $currentTime - (11 * 3600), true];
    }

    /**
     * @dataProvider dataProviderNeedToDeleteAppServer
     *
     * @param int  $currentTime    The current timestamp.
     * @param int  $serverTime     The server timestamp.
     * @param bool $expectedResult Expected result
     */
    public function testNeedToDeleteAppServer($currentTime, $serverTime, $expectedResult)
    {
        $serverNode = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $serverNode->setTimestamp($serverTime);
        $this->assertSame($expectedResult, $serverNode->needToDelete($currentTime));
    }

    /**
     * Data provider for the test method .
     */
    public function dataProviderNeedToDeleteAppServer(): \Iterator
    {
        $currentTime = 1400000000;
        yield [null, $currentTime - (73 * 3600), false];
        // Don't remove server if timestamp is not set at all.
        yield [1, $currentTime - (73 * 3600), false];
        // Don't remove server if timestamp is not valid.
        yield [$currentTime, $currentTime - (73 * 3600), true];
        // Remove server if its TTL has exceeded.
        yield [$currentTime, $currentTime - (11 * 3600), false];
    }

    /**
     * @dataProvider dataProviderNeedToUpdateAppServer
     *
     * @param int  $currentTime    The current timestamp.
     * @param int  $serverTime     The server timestamp.
     * @param bool $expectedResult Expected result
     */
    public function testNeedToUpdateAppServer($currentTime, $serverTime, $expectedResult)
    {
        $serverNode = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);
        $serverNode->setTimestamp($serverTime);
        $this->assertSame($expectedResult, $serverNode->needToUpdate($currentTime));
    }

    /**
     * Data provider for the test method .
     */
    public function dataProviderNeedToUpdateAppServer(): \Iterator
    {
        $currentTime = 1400000000;
        yield [null, $currentTime - (25 * 3600), true];
        // Update server if server time is not set at all.
        yield [$currentTime, $currentTime - (25 * 3600), true];
        // Time when server information must be updated.
        yield [$currentTime, $currentTime - (24 * 3600), true];
        // Exact time when server information must be updated.
        yield [$currentTime, $currentTime - (11 * 3600), false];
        // Time when server information is up to date.
        yield [$currentTime, $currentTime, false];
        // When node time is the same as current time.
        yield [$currentTime, $currentTime + (11 * 3600), true];
    }
}
