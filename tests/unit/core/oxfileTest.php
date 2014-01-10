<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: oxfileTest.php 26841 2010-03-25 13:58:15Z arvydas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxfileTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        $oDb = oxDb::getDb();
        $sQ = "insert into oxfiles (oxid, OXARTID, OXFILENAME, OXSTOREHASH) values ('testId1','_testProd1','testFile','testFileH')";
        $oDb->execute( $sQ );
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->getOne("TRUNCATE TABLE `oxfiles`");
        $this->cleanUpTable( 'oxorder' );
        $this->cleanUpTable( 'oxorderarticles' );
        $this->cleanUpTable( 'oxorderfiles' );
        parent::tearDown();
    }

     /**
     * Test for oxFiles::StartDownload()
     *
     * @return null
     */
    public function testDownload()
    {
        $sFilePath = oxConfig::getInstance()->getConfigParam('sShopDir').'/out/downloads/test.jpg';
        file_put_contents( $sFilePath, 'test jpg file' );

        $oFile = $this->getMock( 'oxFile', array( 'getStoreLocation'));
        $oFile->expects( $this->once() )->method( 'getStoreLocation' )->will( $this->returnValue( $sFilePath ) );
/*
        $oUtilsFile = $this->getMock( 'oxUtilsFile', array( 'getMimeType'));
        $oUtilsFile->expects( $this->once() )->method( 'getMimeType' );
        oxTestModules::addModuleObject( 'oxUtilsFile', $oUtilsFile );*/

        $oUtils = $this->getMock( 'oxUtils', array( 'setHeader'));
        $oUtils->expects( $this->any() )->method( 'setHeader' );
        oxTestModules::addModuleObject( 'oxUtils', $oUtils );

        $oFile->download();
    }

    /**
     * Test for oxFiles::getStoreLocation()
     *
     * @return null
     */
    public function testGetStoreLocation()
    {
        $oFile = $this->getMock( 'oxFile', array( '_getBaseDownloadDirPath', '_getFileLocation'));
        $oFile->expects( $this->once() )->method( '_getBaseDownloadDirPath' )->will( $this->returnValue( 'aa' ) );
        $oFile->expects( $this->once() )->method( '_getFileLocation' )->will( $this->returnValue( 'bb' ) );

        $this->assertEquals( 'aa/bb', $oFile->getStoreLocation());
    }

    /**
     * Test for oxFiles::getStoreLocation()
     *
     * @return null
     */
    public function testGetStoreLocationUnixFullPath()
    {
       oxConfig::getInstance()->setConfigParam( 'sDownloadsDir', '/fullPath' );

        $oFile = $this->getMock( 'oxFile', array( '_getFileLocation'));
        $oFile->expects( $this->once() )->method( '_getFileLocation' )->will( $this->returnValue( 'fileName' ) );

        $this->assertEquals( '/fullPath/fileName', $oFile->getStoreLocation());
    }

    /**
     * Test for oxFiles::getStoreLocation()
     *
     * @return null
     */
    public function testGetStoreLocationRelativePath()
    {
        oxConfig::getInstance()->setConfigParam( 'sDownloadsDir', 'relativePath' );

        $oFile = $this->getMock( 'oxFile', array( '_getFileLocation'));
        $oFile->expects( $this->once() )->method( '_getFileLocation' )->will( $this->returnValue( 'fileName' ) );

        $this->assertEquals( getShopBasePath().'/relativePath/fileName', $oFile->getStoreLocation());
    }

    /**
     * Test for oxFiles::getStoreLocation()
     *
     * @return null
     */
    public function testGetStoreLocationNotSet()
    {
        $oFile = $this->getMock( 'oxFile', array( '_getFileLocation'));
        $oFile->expects( $this->once() )->method( '_getFileLocation' )->will( $this->returnValue( 'fileName' ) );

        $this->assertEquals( getShopBasePath().'/out/downloads/fileName', $oFile->getStoreLocation());
    }


    /**
     * Test for oxFiles::delete()
     *
     * @return null
     */
    public function testDelete()
    {
        $oDb = oxDb::getDb();

        // $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME, OXSTOREHASH) values ('testId1','_testProd1','testFile','testFileH')";
        $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME, OXSTOREHASH) values ('testId2','_testProd1','testFile','testFileH')";
        $aQ[] = "insert into oxfiles (oxid, OXARTID, OXFILENAME, OXSTOREHASH) values ('testId3','_testProd2','testFile1','testFileH1')";

        foreach ( $aQ as $sQ ) {
            $oDb->execute( $sQ );
        }

        if( !is_dir(oxConfig::getInstance()->getConfigParam('sShopDir').'/out/downloads/te') ){
            mkdir( oxConfig::getInstance()->getConfigParam('sShopDir').'/out/downloads/te', 0755);
        }

        $sFilePath1 = oxConfig::getInstance()->getConfigParam('sShopDir').'/out/downloads/te/testFileH';
        file_put_contents( $sFilePath1, 'test jpg file' );

        $oFile = new oxFile();

        $this->assertTrue( $oFile->delete('testId1') );
        $this->assertTrue( is_file($sFilePath1) );
        $this->assertEquals( 2, $oDb->getOne("SELECT COUNT(*) FROM `oxfiles`") );

        $oFile = new oxFile();
        $oFile->load( 'testId2');
        $this->assertTrue( $oFile->delete() );
        $this->assertFalse( is_file( $sFilePath1 ) );
        $this->assertEquals( 1, $oDb->getOne("SELECT COUNT(*) FROM `oxfiles`") );

        $oFile = new oxFile();

        $this->assertFalse( $oFile->delete('testId4') );
    }

    /**
     * Test for oxFiles::processFile()
     *
     * @return null
     */
    public function testProcessFileUploadOK()
    {
        $sFilePath = oxConfig::getInstance()->getConfigParam('sShopDir').'out/downloads/testFile';
        file_put_contents( $sFilePath, 'test jpg file' );

        $sFileHah = md5_file( $sFilePath );

        $aFileInfo = array('tmp_name' => $sFilePath, 'name' => 'testFile');

        $oConfig = $this->getMock( 'oxConfig', array( 'getUploadedFile' ) );
        $oConfig->expects( $this->any() )->method( 'getUploadedFile' )->will( $this->returnValue( $aFileInfo ) );

        $oFile = $this->getMock( 'oxFile', array( 'getConfig', '_uploadFile'), array(), '', false  );
        $oFile->expects( $this->any() )->method( 'getConfig' )->will( $this->returnValue( $oConfig ) );
        $oFile->expects( $this->any() )->method( '_uploadFile' )->will( $this->returnValue( true) );

        $oFile->processFile('aa');

        $this->assertEquals($sFileHah, $oFile->oxfiles__oxstorehash->value);

    }

    /**
     * Test for oxFiles::processFile()
     *
     * @return null
     */
    public function testProcessFileUploadBad()
    {

        $sFilePath = oxConfig::getInstance()->getConfigParam('sShopDir').'out/downloads/testFile';
        file_put_contents( $sFilePath, 'test jpg file' );

        $sFileHah = md5_file( $sFilePath );

        $aFileInfo = array('tmp_name' => $sFilePath, 'name' => 'testFile');

        $oConfig = $this->getMock( 'oxConfig', array( 'getUploadedFile' ) );
        $oConfig->expects( $this->any() )->method( 'getUploadedFile' )->will( $this->returnValue( $aFileInfo ) );

        $oFile = $this->getMock( 'oxFile', array( 'getConfig', '_uploadFile'), array(), '', false  );
        $oFile->expects( $this->any() )->method( 'getConfig' )->will( $this->returnValue( $oConfig ) );
        $oFile->expects( $this->any() )->method( '_uploadFile' )->will( $this->returnValue( false ) );

        // testing..
        try {
            $oFile->processFile('aa');
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "EXCEPTION_COULDNOTWRITETOFILE", $oExcp->getMessage(), "error in oxFiles::processFile()" );
            return;
        }
        $this->fail( "error in oxFiles::processFile()" );

    }

    /**
     * Test for oxFiles::hasValidDownloads()
     *
     * @return null
     */
    public function testHasValidDownloads()
    {


        oxDb::getDB()->execute( 'insert into oxorderfiles
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
                    oxorderarticleid ="_orderarticleId"' );

        $oOrder = new oxOrder();
        $oOrder->setId('_orderId');
        $oOrder->save();

        $oOrderArticle = new oxOrderArticle();
        $oOrderArticle->setId('_orderArticleId');
        $oOrderArticle->save();

        $oFile = new oxFile();
        $oFile->setId("fileId");
        $this->assertTrue( $oFile->hasValidDownloads() );
    }

    /**
     * Test for oxFiles::hasValidDownloads()
     *
     * @return null
     */
    public function testHasValidDownloadsFalse()
    {
        $oFile = new oxFile();
        $this->assertFalse( $oFile->hasValidDownloads() );
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
     *
     * @return null
     */
    public function testGetGlobalMaxDownloadsCount()
    {
        modConfig::getInstance()->setConfigParam( "iMaxDownloadsCount", 2 );
        $oFile = new oxFile();
        $oFile->oxfiles__oxmaxdownloads = new oxField(-1);
        $this->assertEquals( 2, $oFile->getMaxDownloadsCount() );
    }

    /**
     * Test for oxFiles::getMaxDownloadCount()
     *
     * @return null
     */
    public function testGetMaxDownloadsCount()
    {
        modConfig::getInstance()->setConfigParam( "iMaxDownloadsCount", 2 );
        $oFile = new oxFile();
        $oFile->oxfiles__oxmaxdownloads = new oxField(0);
        $this->assertEquals( 0, $oFile->getMaxDownloadsCount() );
    }

    /**
     * Test for oxFiles::getMaxUnregisteredDownloadCount()
     *
     * @return null
     */
    public function testGetGlobalMaxUnregisteredDownloadsCount()
    {
        modConfig::getInstance()->setConfigParam( "iMaxDownloadsCountUnregistered", 2 );
        $oFile = new oxFile();
        $oFile->oxfiles__oxmaxunregdownloads = new oxField(-1);
        $this->assertEquals( 2, $oFile->getMaxUnregisteredDownloadsCount() );
    }

    /**
     * Test for oxFiles::getMaxUnregisteredDownloadCount()
     *
     * @return null
     */
    public function testGetMaxUnregisteredDownloadsCount()
    {
        modConfig::getInstance()->setConfigParam( "iMaxDownloadsCountUnregistered", 2 );
        $oFile = new oxFile();
        $oFile->oxfiles__oxmaxunregdownloads = new oxField(0);
        $this->assertEquals( 0, $oFile->getMaxUnregisteredDownloadsCount() );
    }

    /**
     * Test for oxFiles::getLinkExpirationTime()
     *
     * @return null
     */
    public function testGetGlobalLinkExpirationTime()
    {
        modConfig::getInstance()->setConfigParam( "iLinkExpirationTime", 2 );
        $oFile = new oxFile();
        $oFile->oxfiles__oxlinkexptime = new oxField(-1);
        $this->assertEquals( 2, $oFile->getLinkExpirationTime() );
    }

    /**
     * Test for oxFiles::getLinkExpirationTime()
     *
     * @return null
     */
    public function testGetLinkExpirationTime()
    {
        modConfig::getInstance()->setConfigParam( "iLinkExpirationTime", 2 );
        $oFile = new oxFile();
        $oFile->oxfiles__oxlinkexptime = new oxField(0);
        $this->assertEquals( 0, $oFile->getLinkExpirationTime() );
    }

    /**
     * Test for oxFiles::getDownloadExpirationTime()
     *
     * @return null
     */
    public function testGetGlobalDownloadExpirationTime()
    {
        modConfig::getInstance()->setConfigParam( "iDownloadExpirationTime", 2 );
        $oFile = new oxFile();
        $oFile->oxfiles__oxdownloadexptime = new oxField(-1);
        $this->assertEquals( 2, $oFile->getDownloadExpirationTime() );
    }

    /**
     * Test for oxFiles::getDownloadExpirationTime()
     *
     * @return null
     */
    public function testGetDownloadExpirationTime()
    {
        modConfig::getInstance()->setConfigParam( "iDownloadExpirationTime", 2 );
        $oFile = new oxFile();
        $oFile->oxfiles__oxdownloadexptime = new oxField(0);
        $this->assertEquals( 0, $oFile->getDownloadExpirationTime() );
    }

}
