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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Core_oxfileTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        $oDb = oxDb::getDb();
        $sQ = "insert into oxfiles (oxid, OXARTID, OXFILENAME, OXSTOREHASH) values ('testId1','_testProd1','testFile','testFileH')";
        $oDb->execute($sQ);
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $sFilePath = $this->getTestFilePath();
        if (!empty($sFilePath) && file_exists($sFilePath)) {
            unlink($sFilePath);
        }

        oxDb::getDb()->getOne("TRUNCATE TABLE `oxfiles`");
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxorderfiles');
        parent::tearDown();
    }

    /**
     * Test for oxFiles::StartDownload()
     */
    public function testDownload()
    {
        $sFilePath = $this->getConfig()->getConfigParam('sShopDir') . '/out/downloads/test.jpg';
        file_put_contents($sFilePath, 'test jpg file');

        $oFile = $this->getMock('oxFile', array('getStoreLocation'));
        $oFile->expects($this->any())->method('getStoreLocation')->will($this->returnValue($sFilePath));

        $oUtils = $this->getMock('oxUtils', array('setHeader'));
        $oUtils->expects($this->any())->method('setHeader');
        oxTestModules::addModuleObject('oxUtils', $oUtils);

        $oFile->download();
    }

    public function testDownloadThrowExceptionWhenAboveDownloadFolder()
    {
        $this->setExpectedException('oxException');

        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $utils */
        $utils = $this->getMock('oxUtils');
        $utils->expects($this->any())->method('setHeader')->will($this->returnValue(true));
        $utils->expects($this->any())->method('showMessageAndExit')->will($this->returnValue(true));
        oxRegistry::set('oxUtils', $utils);

        $fileName = '../../../config.inc.php';

        $file = oxNew('oxFile');
        $file->oxfiles__oxfilename = new oxField($fileName);

        $file->download();
    }

    public function testDownloadThrowExceptionWhenFileDoesNotExist()
    {
        $this->setExpectedException('oxException');

        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $utils */
        $utils = $this->getMock('oxUtils');
        $utils->expects($this->any())->method('setHeader')->will($this->returnValue(true));
        $utils->expects($this->any())->method('showMessageAndExit')->will($this->returnValue(true));
        oxRegistry::set('oxUtils', $utils);

        $fileName = 'some_not_existing_file';

        $file = oxNew('oxFile');
        $file->oxfiles__oxfilename = new oxField($fileName);

        $file->download();
    }

    /**
     * Test for oxFiles::getStoreLocation()
     */
    public function testGetStoreLocation()
    {
        $oFile = $this->getMock('oxFile', array('_getBaseDownloadDirPath', '_getFileLocation'));
        $oFile->expects($this->once())->method('_getBaseDownloadDirPath')->will($this->returnValue('aa'));
        $oFile->expects($this->once())->method('_getFileLocation')->will($this->returnValue('bb'));

        $this->assertEquals('aa/bb', $oFile->getStoreLocation());
    }

    /**
     * Test for oxFiles::getStoreLocation()
     */
    public function testGetStoreLocationUnixFullPath()
    {
        $this->getConfig()->setConfigParam('sDownloadsDir', '/fullPath');

        $oFile = $this->getMock('oxFile', array('_getFileLocation'));
        $oFile->expects($this->once())->method('_getFileLocation')->will($this->returnValue('fileName'));

        $this->assertEquals('/fullPath/fileName', $oFile->getStoreLocation());
    }

    /**
     * Test for oxFiles::getStoreLocation()
     */
    public function testGetStoreLocationRelativePath()
    {
        $this->getConfig()->setConfigParam('sDownloadsDir', 'relativePath');

        $oFile = $this->getMock('oxFile', array('_getFileLocation'));
        $oFile->expects($this->once())->method('_getFileLocation')->will($this->returnValue('fileName'));

        $this->assertEquals(getShopBasePath() . '/relativePath/fileName', $oFile->getStoreLocation());
    }

    /**
     * Test for oxFiles::getStoreLocation()
     */
    public function testGetStoreLocationNotSet()
    {
        $oFile = $this->getMock('oxFile', array('_getFileLocation'));
        $oFile->expects($this->once())->method('_getFileLocation')->will($this->returnValue('fileName'));

        $this->assertEquals(getShopBasePath() . '/out/downloads/fileName', $oFile->getStoreLocation());
    }

    public function testStorageLocationIsUnderDownloadFolder()
    {
        $fileName = '../e4/e48a1b571bd2d2e60fb2d9b1b76b34d4';

        $file = oxNew('oxFile');
        $file->oxfiles__oxfilename = new oxField($fileName);

        $this->assertTrue($file->isUnderDownloadFolder());
    }

    public function testStorageLocationIsNotUnderDownloadFolder()
    {
        $fileName = '../../../config.inc.php';

        $file = oxNew('oxFile');
        $file->oxfiles__oxfilename = new oxField($fileName);

        $this->assertFalse($file->isUnderDownloadFolder());
    }

    public function testStorageLocationWithNotExistingFile()
    {
        $fileName = '../../../not_existing_file';

        $file = oxNew('oxFile');
        $file->oxfiles__oxfilename = new oxField($fileName);

        $this->assertFalse($file->isUnderDownloadFolder());
    }

    /**
     * Test for oxFiles::delete()
     */
    public function testDelete()
    {
        $oDb = oxDb::getDb();

        $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME, OXSTOREHASH) values ('testId2','_testProd1','testFile','testFileH')";
        $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME, OXSTOREHASH) values ('testId3','_testProd2','testFile1','testFileH1')";

        foreach ($aQ as $sQ) {
            $oDb->execute($sQ);
        }

        if (!is_dir($this->getConfig()->getConfigParam('sShopDir') . '/out/downloads/te')) {
            mkdir($this->getConfig()->getConfigParam('sShopDir') . '/out/downloads/te', 0755);
        }

        $sFilePath1 = $this->getConfig()->getConfigParam('sShopDir') . '/out/downloads/te/testFileH';
        file_put_contents($sFilePath1, 'test jpg file');

        $oFile = new oxFile();

        $this->assertTrue($oFile->delete('testId1'));
        $this->assertTrue(is_file($sFilePath1));
        $this->assertEquals(2, $oDb->getOne("SELECT COUNT(*) FROM `oxfiles`"));

        $oFile = new oxFile();
        $oFile->load('testId2');
        $this->assertTrue($oFile->delete());
        $this->assertFalse(is_file($sFilePath1));
        $this->assertEquals(1, $oDb->getOne("SELECT COUNT(*) FROM `oxfiles`"));

        $oFile = new oxFile();

        $this->assertFalse($oFile->delete('testId4'));
    }

    /**
     * Test for oxFiles::processFile()
     */
    public function testProcessFileUploadOK()
    {
        $sFilePath = $this->getTestFilePath();
        file_put_contents($sFilePath, 'test jpg file');

        $sFileHah = md5_file($sFilePath);

        $aFileInfo = array('tmp_name' => $sFilePath, 'name' => 'testFile');

        $oConfig = $this->getMock('oxConfig', array('getUploadedFile'));
        $oConfig->expects($this->any())->method('getUploadedFile')->will($this->returnValue($aFileInfo));

        $oFile = $this->getMock('oxFile', array('getConfig', '_uploadFile'), array(), '', false);
        $oFile->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oFile->expects($this->any())->method('_uploadFile')->will($this->returnValue(true));

        $oFile->processFile('aa');

        $this->assertEquals($sFileHah, $oFile->oxfiles__oxstorehash->value);

    }

    /**
     * Test for oxFiles::processFile()
     */
    public function testProcessFileUploadBad()
    {
        $sFilePath = $this->getTestFilePath();
        file_put_contents($sFilePath, 'test jpg file');

        $aFileInfo = array('tmp_name' => $sFilePath, 'name' => 'testFile');

        $oConfig = $this->getMock('oxConfig', array('getUploadedFile'));
        $oConfig->expects($this->any())->method('getUploadedFile')->will($this->returnValue($aFileInfo));

        $oFile = $this->getMock('oxFile', array('getConfig', '_uploadFile'), array(), '', false);
        $oFile->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oFile->expects($this->any())->method('_uploadFile')->will($this->returnValue(false));

        // testing..
        try {
            $oFile->processFile('aa');
        } catch (Exception $oException) {
            $this->assertEquals("EXCEPTION_COULDNOTWRITETOFILE", $oException->getMessage(), "error in oxFiles::processFile()");

            return;
        }
        $this->fail("error in oxFiles::processFile()");

    }

    /**
     * Test for oxFiles::hasValidDownloads()
     */
    public function testHasValidDownloads()
    {


        oxDb::getDB()->execute(
            'insert into oxorderfiles
                           set
                               oxid="_testOrderFile",
                               oxfileid="fileId",
                               oxmaxdownloadcount="10",
                               oxlinkexpirationtime="24",
                               oxdownloadexpirationtime="12",
                               oxvaliduntil="2050-10-50 12:12:00",
                               oxdownloadcount="2",
                               oxfirstdownload="2011-10-10",
                               oxlastdownload="2011-10-20",
                               oxorderid = "_orderId",
                               oxorderarticleid ="_orderarticleId"'
        );

        $oOrder = new oxOrder();
        $oOrder->setId('_orderId');
        $oOrder->save();

        $oOrderArticle = new oxOrderArticle();
        $oOrderArticle->setId('_orderArticleId');
        $oOrderArticle->save();

        $oFile = new oxFile();
        $oFile->setId("fileId");
        $this->assertTrue($oFile->hasValidDownloads());
    }

    /**
     * Test for oxFiles::hasValidDownloads()
     */
    public function testHasValidDownloadsFalse()
    {
        $oFile = new oxFile();
        $this->assertFalse($oFile->hasValidDownloads());
    }

    /**
     * Tests oxFile::isUploaded() method
     */
    public function testIsUploaded()
    {
        $oSubj = new oxFile();
        $oSubj->oxfiles__oxstorehash = new oxField("hash5");
        $this->assertTrue($oSubj->isUploaded());
    }

    /**
     * Tests oxFile::isUploaded() method negative output
     */
    public function testIsUploadedNegative()
    {
        $oSubj = new oxFile();
        $oSubj->oxfiles__oxstorehash = new oxField("");
        $this->assertFalse($oSubj->isUploaded());
    }

    /**
     * Test for oxFiles::getMaxDownloadCount()
     */
    public function testGetGlobalMaxDownloadsCount()
    {
        $this->getConfig()->setConfigParam("iMaxDownloadsCount", 2);
        $oFile = new oxFile();
        $oFile->oxfiles__oxmaxdownloads = new oxField(-1);
        $this->assertEquals(2, $oFile->getMaxDownloadsCount());
    }

    /**
     * Test for oxFiles::getMaxDownloadCount()
     */
    public function testGetMaxDownloadsCount()
    {
        $this->getConfig()->setConfigParam("iMaxDownloadsCount", 2);
        $oFile = new oxFile();
        $oFile->oxfiles__oxmaxdownloads = new oxField(0);
        $this->assertEquals(0, $oFile->getMaxDownloadsCount());
    }

    /**
     * Test for oxFiles::getMaxUnregisteredDownloadCount()
     */
    public function testGetGlobalMaxUnregisteredDownloadsCount()
    {
        $this->getConfig()->setConfigParam("iMaxDownloadsCountUnregistered", 2);
        $oFile = new oxFile();
        $oFile->oxfiles__oxmaxunregdownloads = new oxField(-1);
        $this->assertEquals(2, $oFile->getMaxUnregisteredDownloadsCount());
    }

    /**
     * Test for oxFiles::getMaxUnregisteredDownloadCount()
     */
    public function testGetMaxUnregisteredDownloadsCount()
    {
        $this->getConfig()->setConfigParam("iMaxDownloadsCountUnregistered", 2);
        $oFile = new oxFile();
        $oFile->oxfiles__oxmaxunregdownloads = new oxField(0);
        $this->assertEquals(0, $oFile->getMaxUnregisteredDownloadsCount());
    }

    /**
     * Test for oxFiles::getLinkExpirationTime()
     */
    public function testGetGlobalLinkExpirationTime()
    {
        $this->getConfig()->setConfigParam("iLinkExpirationTime", 2);
        $oFile = new oxFile();
        $oFile->oxfiles__oxlinkexptime = new oxField(-1);
        $this->assertEquals(2, $oFile->getLinkExpirationTime());
    }

    /**
     * Test for oxFiles::getLinkExpirationTime()
     */
    public function testGetLinkExpirationTime()
    {
        $this->getConfig()->setConfigParam("iLinkExpirationTime", 2);
        $oFile = new oxFile();
        $oFile->oxfiles__oxlinkexptime = new oxField(0);
        $this->assertEquals(0, $oFile->getLinkExpirationTime());
    }

    /**
     * Test for oxFiles::getDownloadExpirationTime()
     */
    public function testGetGlobalDownloadExpirationTime()
    {
        $this->getConfig()->setConfigParam("iDownloadExpirationTime", 2);
        $oFile = new oxFile();
        $oFile->oxfiles__oxdownloadexptime = new oxField(-1);
        $this->assertEquals(2, $oFile->getDownloadExpirationTime());
    }

    /**
     * Test for oxFiles::getDownloadExpirationTime()
     */
    public function testGetDownloadExpirationTime()
    {
        $this->getConfig()->setConfigParam("iDownloadExpirationTime", 2);
        $oFile = new oxFile();
        $oFile->oxfiles__oxdownloadexptime = new oxField(0);
        $this->assertEquals(0, $oFile->getDownloadExpirationTime());
    }

    /**
     * Get path to test file.
     *
     * @return string
     */
    protected function getTestFilePath()
    {
        return $this->getConfig()->getConfigParam('sShopDir') . 'out/downloads/testFile';
    }
}
