<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;
use OxidEsales\EshopCommunity\Core\Field;
use \oxTestModules;

class FileTest extends \OxidTestCase
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
        oxDb::getDb()->execute("TRUNCATE TABLE `oxfiles`");
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
        $sFilePath = $this->createFile('out/downloads/test.jpg', 'test jpg file');

        /** @var oxFile|PHPUnit\Framework\MockObject\MockObject $oFile */
        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('getStoreLocation', 'isUnderDownloadFolder'));
        $oFile->expects($this->any())->method('getStoreLocation')->will($this->returnValue($sFilePath));
        $oFile->expects($this->any())->method('isUnderDownloadFolder')->will($this->returnValue(true));

        /** @var oxUtils|PHPUnit\Framework\MockObject\MockObject $oUtils */
        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('setHeader', 'showMessageAndExit'));
        $oUtils->expects($this->any())->method('setHeader');
        $oUtils->expects($this->once())->method('showMessageAndExit');
        oxTestModules::addModuleObject('oxUtils', $oUtils);

        ob_start();
        $oFile->download();

        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('test jpg file', $content);
    }

    public function testDownloadThrowExceptionWhenAboveDownloadFolder()
    {
        $this->expectException('oxException');

        /** @var oxUtils|PHPUnit\Framework\MockObject\MockObject $utils */
        $utils = $this->getMock('oxUtils');
        $utils->expects($this->any())->method('setHeader')->will($this->returnValue(true));
        $utils->expects($this->any())->method('showMessageAndExit')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(oxUtils::class, $utils);

        $fileName = '../../../config.inc.php';

        $file = oxNew('oxFile');
        $file->oxfiles__oxfilename = new oxField($fileName);

        $file->download();
    }

    public function testDownloadThrowExceptionWhenFileDoesNotExist()
    {
        $this->expectException('oxException');

        /** @var oxUtils|PHPUnit\Framework\MockObject\MockObject $utils */
        $utils = $this->getMock('oxUtils');
        $utils->expects($this->any())->method('setHeader')->will($this->returnValue(true));
        $utils->expects($this->any())->method('showMessageAndExit')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

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
        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('_getBaseDownloadDirPath', '_getFileLocation'));
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

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('_getFileLocation'));
        $oFile->expects($this->once())->method('_getFileLocation')->will($this->returnValue('fileName'));

        $this->assertEquals('/fullPath/fileName', $oFile->getStoreLocation());
    }

    /**
     * Test for oxFiles::getStoreLocation()
     */
    public function testGetStoreLocationRelativePath()
    {
        $this->getConfig()->setConfigParam('sDownloadsDir', 'relativePath');

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('_getFileLocation'));
        $oFile->expects($this->once())->method('_getFileLocation')->will($this->returnValue('fileName'));

        $this->assertEquals(getShopBasePath() . '/relativePath/fileName', $oFile->getStoreLocation());
    }

    /**
     * Test for oxFiles::getStoreLocation()
     */
    public function testGetStoreLocationNotSet()
    {
        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('_getFileLocation'));
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

        $vfsStream = $this->getVfsStreamWrapper();

        $filePath = $vfsStream->createFile('out/downloads/te/testFileH', 'test jpg file');
        $this->getConfig()->setConfigParam('sShopDir', $vfsStream->getRootPath());

        $oFile = oxNew('oxFile');

        $this->assertTrue($oFile->delete('testId1'));
        $this->assertTrue(is_file($filePath));
        $this->assertEquals(2, $oDb->getOne("SELECT COUNT(*) FROM `oxfiles`"));

        $oFile = oxNew('oxFile');
        $oFile->load('testId2');
        $this->assertTrue($oFile->delete());
        $this->assertFalse(is_file($filePath));
        $this->assertEquals(1, $oDb->getOne("SELECT COUNT(*) FROM `oxfiles`"));

        $oFile = oxNew('oxFile');

        $this->assertFalse($oFile->delete('testId4'));
    }

    /**
     * Test for oxFiles::processFile()
     */
    public function testProcessFileUploadOK()
    {
        $filePath = $this->createFile('out/downloads/testFile', 'test jpg file');

        $sFileHah = md5_file($filePath);

        $aFileInfo = array('tmp_name' => $filePath, 'name' => 'testFile');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getUploadedFile'));
        $oConfig->expects($this->any())->method('getUploadedFile')->will($this->returnValue($aFileInfo));

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('getConfig', '_uploadFile', '_getHashedFileDir'), array(), '', false);
        $oFile->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oFile->expects($this->any())->method('_uploadFile')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('_getHashedFileDir')->will($this->returnValue('eb'));

        $oFile->processFile('aa');

        $this->assertEquals($sFileHah, $oFile->oxfiles__oxstorehash->value);
    }

    /**
     * Test for oxFiles::processFile()
     */
    public function testProcessFileUploadBad()
    {
        $this->expectException('oxException');
        $this->expectExceptionMessage("EXCEPTION_COULDNOTWRITETOFILE");

        $filePath = $this->createFile('out/downloads/testFile', 'test jpg file');

        $aFileInfo = array('tmp_name' => $filePath, 'name' => 'testFile');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getUploadedFile'));
        $oConfig->expects($this->any())->method('getUploadedFile')->will($this->returnValue($aFileInfo));

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('getConfig', '_uploadFile', '_getHashedFileDir'), array(), '', false);
        $oFile->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oFile->expects($this->any())->method('_uploadFile')->will($this->returnValue(false));
        $oFile->expects($this->any())->method('_getHashedFileDir')->will($this->returnValue('eb'));

        $oFile->processFile('aa');
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

        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_orderId');
        $oOrder->save();

        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->setId('_orderArticleId');
        $oOrderArticle->oxorderarticles__oxorderid = new Field($oOrder->getId());
        $oOrderArticle->save();

        $oFile = oxNew('oxFile');
        $oFile->setId("fileId");
        $this->assertTrue($oFile->hasValidDownloads());
    }

    /**
     * Test for oxFiles::hasValidDownloads()
     */
    public function testHasValidDownloadsFalse()
    {
        $oFile = oxNew('oxFile');
        $this->assertFalse($oFile->hasValidDownloads());
    }

    /**
     * Tests oxFile::isUploaded() method
     */
    public function testIsUploaded()
    {
        $oSubj = oxNew('oxFile');
        $oSubj->oxfiles__oxstorehash = new oxField("hash5");
        $this->assertTrue($oSubj->isUploaded());
    }

    /**
     * Tests oxFile::isUploaded() method negative output
     */
    public function testIsUploadedNegative()
    {
        $oSubj = oxNew('oxFile');
        $oSubj->oxfiles__oxstorehash = new oxField("");
        $this->assertFalse($oSubj->isUploaded());
    }

    /**
     * Test for oxFiles::getMaxDownloadCount()
     */
    public function testGetGlobalMaxDownloadsCount()
    {
        $this->getConfig()->setConfigParam("iMaxDownloadsCount", 2);
        $oFile = oxNew('oxFile');
        $oFile->oxfiles__oxmaxdownloads = new oxField(-1);
        $this->assertEquals(2, $oFile->getMaxDownloadsCount());
    }

    /**
     * Test for oxFiles::getMaxDownloadCount()
     */
    public function testGetMaxDownloadsCount()
    {
        $this->getConfig()->setConfigParam("iMaxDownloadsCount", 2);
        $oFile = oxNew('oxFile');
        $oFile->oxfiles__oxmaxdownloads = new oxField(0);
        $this->assertEquals(0, $oFile->getMaxDownloadsCount());
    }

    /**
     * Test for oxFiles::getMaxUnregisteredDownloadCount()
     */
    public function testGetGlobalMaxUnregisteredDownloadsCount()
    {
        $this->getConfig()->setConfigParam("iMaxDownloadsCountUnregistered", 2);
        $oFile = oxNew('oxFile');
        $oFile->oxfiles__oxmaxunregdownloads = new oxField(-1);
        $this->assertEquals(2, $oFile->getMaxUnregisteredDownloadsCount());
    }

    /**
     * Test for oxFiles::getMaxUnregisteredDownloadCount()
     */
    public function testGetMaxUnregisteredDownloadsCount()
    {
        $this->getConfig()->setConfigParam("iMaxDownloadsCountUnregistered", 2);
        $oFile = oxNew('oxFile');
        $oFile->oxfiles__oxmaxunregdownloads = new oxField(0);
        $this->assertEquals(0, $oFile->getMaxUnregisteredDownloadsCount());
    }

    /**
     * Test for oxFiles::getLinkExpirationTime()
     */
    public function testGetGlobalLinkExpirationTime()
    {
        $this->getConfig()->setConfigParam("iLinkExpirationTime", 2);
        $oFile = oxNew('oxFile');
        $oFile->oxfiles__oxlinkexptime = new oxField(-1);
        $this->assertEquals(2, $oFile->getLinkExpirationTime());
    }

    /**
     * Test for oxFiles::getLinkExpirationTime()
     */
    public function testGetLinkExpirationTime()
    {
        $this->getConfig()->setConfigParam("iLinkExpirationTime", 2);
        $oFile = oxNew('oxFile');
        $oFile->oxfiles__oxlinkexptime = new oxField(0);
        $this->assertEquals(0, $oFile->getLinkExpirationTime());
    }

    /**
     * Test for oxFiles::getDownloadExpirationTime()
     */
    public function testGetGlobalDownloadExpirationTime()
    {
        $this->getConfig()->setConfigParam("iDownloadExpirationTime", 2);
        $oFile = oxNew('oxFile');
        $oFile->oxfiles__oxdownloadexptime = new oxField(-1);
        $this->assertEquals(2, $oFile->getDownloadExpirationTime());
    }

    /**
     * Test for oxFiles::getDownloadExpirationTime()
     */
    public function testGetDownloadExpirationTime()
    {
        $this->getConfig()->setConfigParam("iDownloadExpirationTime", 2);
        $oFile = oxNew('oxFile');
        $oFile->oxfiles__oxdownloadexptime = new oxField(0);
        $this->assertEquals(0, $oFile->getDownloadExpirationTime());
    }
}
