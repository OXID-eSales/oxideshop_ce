<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

class DiagnosticsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing version getter and setter
     */
    public function testGetVersion()
    {
        $oChecker = oxNew("oxDiagnostics");
        $oChecker->setVersion("v123");

        $this->assertSame("v123", $oChecker->getVersion());
    }

    /**
     * Testing edition getter and setter
     */
    public function testGetEdition()
    {
        $oChecker = oxNew("oxDiagnostics");
        $oChecker->setEdition("e123");

        $this->assertSame("e123", $oChecker->getEdition());
    }

    /**
     * Testing base directory getter and setter
     */
    public function testGetShopLink()
    {
        $oChecker = oxNew("oxDiagnostics");
        $oChecker->setShopLink("somelink");

        $this->assertSame("somelink", $oChecker->getShopLink());
    }

    /**
     * Setting up test for getShopDetails
     */
    protected function setUpTestGetShopDetails()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("DELETE FROM `oxshops` WHERE `oxid` > 1");

        for ($i = 2; $i < 5; $i++) {
            $oDb->execute(sprintf('INSERT INTO `oxshops` (OXID, OXACTIVE, OXNAME) VALUES (%d, ', $i) . ($i % 2) . sprintf(", 'Test Shop %d')", $i));
        }

        $oDb->execute("DELETE FROM `oxcategories`");

        for ($i = 3; $i < 12; $i++) {
            $oDb->execute(
                "Insert into oxcategories (`OXID`,`OXROOTID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXACTIVE`,`OXPRICEFROM`,`OXPRICETO`)" .
                "values ('test" . $i . "','test','1','4','test'," . ($i % 2) . ",'10','50')"
            );
        }

        $this->getDb()->execute("delete from `oxarticles` ");
        for ($i = 2; $i < 9; $i++) {
            $oDb->execute(
                "INSERT INTO `oxarticles` (`OXID`, `OXSHOPID`, `OXPARENTID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXARTNUM` ) VALUES " .
                "('_testArtId" . $i . "', " . ShopIdCalculator::BASE_SHOP_ID . ", '', " . ($i % 2) . ", '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0802-85-823-7-1')"
            );
        }

        $this->getDb()->execute("delete from `oxuser` ");
        for ($i = 2; $i < 11; $i++) {
            $oDb->execute(
                "INSERT INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXTIMESTAMP`) " .
                " VALUES ('test_id" . $i . "', " . ($i % 2) . ", '', '1', 'test" . $i . "', '', '', CURRENT_TIMESTAMP)"
            );
        }
    }

    /**
     * Testing getShopDetails
     */
    public function testGetShopDetails()
    {
        $this->setUpTestGetShopDetails();

        $oDiagnostics = new \OxidEsales\Eshop\Application\Model\Diagnostics();

        $oDiagnostics->setShopLink('someShopURL');
        $oDiagnostics->setEdition('someEdition');
        $oDiagnostics->setVersion('someVersion');

        $aResult = $oDiagnostics->getShopDetails();

        $this->assertCount(11, $aResult);
        $this->assertSame('someShopURL', $aResult['URL']);
        $this->assertSame('someEdition', $aResult['Edition']);
        $this->assertSame('someVersion', $aResult['Version']);
        $this->assertSame(4, $aResult['Subshops (Total)']);
        $this->assertSame(2, $aResult['Subshops (Active)']);
        $this->assertSame(9, $aResult['Categories (Total)']);
        $this->assertSame(5, $aResult['Categories (Active)']);
        $this->assertSame(7, $aResult['Articles (Total)']);
        $this->assertSame(3, $aResult['Articles (Active)']);
        $this->assertSame(9, $aResult['Users (Total)']);
    }


    /**
     * Testing getServerInfo
     */
    public function testGetServerInfo()
    {
        $oDiagnostics = $this->getMock(
            'oxDiagnostics',
            ['getCpuAmount', 'getCpuMhz', 'getBogoMips', 'getMemoryTotal', 'getMemoryFree', 'getCpuModel', 'getVirtualizationSystem', 'getApacheVersion', 'isExecAllowed', 'getPhpVersion', 'getMySqlServerInfo', 'getDiskTotalSpace', 'getDiskFreeSpace']
        );

        $oDiagnostics->expects($this->once())->method('getCpuAmount')->willReturn(5);
        $oDiagnostics->expects($this->once())->method('getCpuMhz')->willReturn(500);
        $oDiagnostics->expects($this->once())->method('getBogoMips')->willReturn(1000);
        $oDiagnostics->expects($this->once())->method('getMemoryTotal')->willReturn("3000");
        $oDiagnostics->expects($this->once())->method('getMemoryFree')->willReturn("1234");
        $oDiagnostics->expects($this->once())->method('getCpuModel')->willReturn("Cpu Model");
        $oDiagnostics->expects($this->once())->method('getVirtualizationSystem')->willReturn("LINUX");
        $oDiagnostics->expects($this->once())->method('getApacheVersion')->willReturn("321");
        $oDiagnostics->expects($this->once())->method('getPhpVersion')->willReturn("654");
        $oDiagnostics->expects($this->once())->method('getMySqlServerInfo')->willReturn("MySQL information");
        $oDiagnostics->expects($this->once())->method('getDiskTotalSpace')->willReturn(9999);
        $oDiagnostics->expects($this->once())->method('getDiskFreeSpace')->willReturn(3333);
        $oDiagnostics->method('isExecAllowed')->willReturn(true);

        $aServerInfo = $oDiagnostics->getServerInfo();

        $this->assertCount(12, $aServerInfo);
        $this->assertSame('LINUX', $aServerInfo['VM']);
        $this->assertSame("321", $aServerInfo['Apache']);
        $this->assertSame("654", $aServerInfo['PHP']);
        $this->assertSame("MySQL information", $aServerInfo['MySQL']);
        $this->assertSame(9999, $aServerInfo['Disk total']);
        $this->assertSame(3333, $aServerInfo['Disk free']);
        $this->assertSame(3000, $aServerInfo['Memory total']);
        $this->assertSame(1234, $aServerInfo['Memory free']);
        $this->assertSame('5x Cpu Model', $aServerInfo['CPU Model']);
        $this->assertSame('500 MHz', $aServerInfo['CPU frequency']);
        $this->assertSame(2, $aServerInfo['CPU cores']);
    }
}
