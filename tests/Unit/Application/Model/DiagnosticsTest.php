<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

class DiagnosticsTest extends \OxidTestCase
{

    /**
     * Testing version getter and setter
     */
    public function testGetVersion()
    {
        $oChecker = oxNew("oxDiagnostics");
        $oChecker->setVersion("v123");

        $this->assertEquals("v123", $oChecker->getVersion());
    }

    /**
     * Testing edition getter and setter
     */
    public function testGetEdition()
    {
        $oChecker = oxNew("oxDiagnostics");
        $oChecker->setEdition("e123");

        $this->assertEquals("e123", $oChecker->getEdition());
    }

    /**
     * Testing revision getter and setter
     */
    public function testGetRevision()
    {
        $oChecker = oxNew("oxDiagnostics");
        $oChecker->setRevision("r123");

        $this->assertEquals("r123", $oChecker->getRevision());
    }

    /**
     * Testing base directory getter and setter
     */
    public function testGetShopLink()
    {
        $oChecker = oxNew("oxDiagnostics");
        $oChecker->setShopLink("somelink");

        $this->assertEquals("somelink", $oChecker->getShopLink());
    }


    /**
     * Testing FileCheckerPathList getter and setter
     */
    public function testGetFileCheckerPathList()
    {
        $oDiagnostics = oxNew("oxDiagnostics");
        $oDiagnostics->setFileCheckerPathList(array("admin", "views"));

        $this->assertEquals(2, count($oDiagnostics->getFileCheckerPathList()));
        $this->assertContains("admin", $oDiagnostics->getFileCheckerPathList());
        $this->assertContains("views", $oDiagnostics->getFileCheckerPathList());
    }

    /**
     * Testing FileCheckerPathList getter and setter
     */
    public function testGetFileCheckerExtensionList()
    {
        $oDiagnostics = oxNew("oxDiagnostics");
        $oDiagnostics->setFileCheckerExtensionList(array("ex1", "ex2"));

        $this->assertEquals(2, count($oDiagnostics->getFileCheckerExtensionList()));
        $this->assertContains("ex1", $oDiagnostics->getFileCheckerExtensionList());
        $this->assertContains("ex2", $oDiagnostics->getFileCheckerExtensionList());
    }

    /**
     * Setting up test for getShopDetails
     */
    protected function _setUpTestGetShopDetails()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("DELETE FROM `oxshops` WHERE `oxid` > 1");

        for ($i = 2; $i < 5; $i++) {
            $oDb->execute("INSERT INTO `oxshops` (OXID, OXACTIVE, OXNAME) VALUES ($i, " . ($i % 2) . ", 'Test Shop $i')");
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
                "('_testArtId" . $i . "', ".ShopIdCalculator::BASE_SHOP_ID.", '', " . ($i % 2) . ", '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0802-85-823-7-1')"
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
        $this->_setUpTestGetShopDetails();

        $oDiagnostics = oxNew('oxDiagnostics');

        $oDiagnostics->setShopLink('someShopURL');
        $oDiagnostics->setEdition('someEdition');
        $oDiagnostics->setVersion('someVersion');
        $oDiagnostics->setRevision('someRevision');

        $aResult = $oDiagnostics->getShopDetails();

        $this->assertEquals(12, count($aResult));
        $this->assertEquals('someShopURL', $aResult['URL']);
        $this->assertEquals('someEdition', $aResult['Edition']);
        $this->assertEquals('someVersion', $aResult['Version']);
        $this->assertEquals('someRevision', $aResult['Revision']);
        $this->assertEquals(4, $aResult['Subshops (Total)']);
        $this->assertEquals(2, $aResult['Subshops (Active)']);
        $this->assertEquals(9, $aResult['Categories (Total)']);
        $this->assertEquals(5, $aResult['Categories (Active)']);
        $this->assertEquals(7, $aResult['Articles (Total)']);
        $this->assertEquals(3, $aResult['Articles (Active)']);
        $this->assertEquals(9, $aResult['Users (Total)']);
    }


    /**
     * Testing getServerInfo
     */
    public function testGetServerInfo()
    {
        $oDiagnostics = $this->getMock(
            'oxDiagnostics',
            array('_getCpuAmount', '_getCpuMhz', '_getBogoMips',
                                   '_getMemoryTotal', '_getMemoryFree', '_getCpuModel', '_getVirtualizationSystem', '_getApacheVersion',
                                   'isExecAllowed', '_getPhpVersion', '_getMySqlServerInfo', '_getDiskTotalSpace', '_getDiskFreeSpace')
        );

        $oDiagnostics->expects($this->once())->method('_getCpuAmount')->will($this->returnValue(5));
        $oDiagnostics->expects($this->once())->method('_getCpuMhz')->will($this->returnValue(500));
        $oDiagnostics->expects($this->once())->method('_getBogoMips')->will($this->returnValue(1000));
        $oDiagnostics->expects($this->once())->method('_getMemoryTotal')->will($this->returnValue("3000"));
        $oDiagnostics->expects($this->once())->method('_getMemoryFree')->will($this->returnValue("1234"));
        $oDiagnostics->expects($this->once())->method('_getCpuModel')->will($this->returnValue("Cpu Model"));
        $oDiagnostics->expects($this->once())->method('_getVirtualizationSystem')->will($this->returnValue("LINUX"));
        $oDiagnostics->expects($this->once())->method('_getApacheVersion')->will($this->returnValue("321"));
        $oDiagnostics->expects($this->once())->method('_getPhpVersion')->will($this->returnValue("654"));
        $oDiagnostics->expects($this->once())->method('_getMySqlServerInfo')->will($this->returnValue("MySQL information"));
        $oDiagnostics->expects($this->once())->method('_getDiskTotalSpace')->will($this->returnValue(9999));
        $oDiagnostics->expects($this->once())->method('_getDiskFreeSpace')->will($this->returnValue(3333));
        $oDiagnostics->expects($this->any())->method('isExecAllowed')->will($this->returnValue(true));

        $aServerInfo = $oDiagnostics->getServerInfo();

        $this->assertEquals(12, count($aServerInfo));
        $this->assertEquals('LINUX', $aServerInfo['VM']);
        $this->assertEquals("321", $aServerInfo['Apache']);
        $this->assertEquals("654", $aServerInfo['PHP']);
        $this->assertEquals("MySQL information", $aServerInfo['MySQL']);
        $this->assertEquals(9999, $aServerInfo['Disk total']);
        $this->assertEquals(3333, $aServerInfo['Disk free']);
        $this->assertEquals(3000, $aServerInfo['Memory total']);
        $this->assertEquals(1234, $aServerInfo['Memory free']);
        $this->assertEquals('5x Cpu Model', $aServerInfo['CPU Model']);
        $this->assertEquals('500 MHz', $aServerInfo['CPU frequency']);
        $this->assertEquals(2, $aServerInfo['CPU cores']);
    }
}
