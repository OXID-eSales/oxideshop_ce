<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use oxfield;
use OxidEsales\Eshop\Core\Field;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for Article_Main class
 */
class ArticleFilesTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("delete from oxfiles where oxid='_testFileId'");
        $oDb->execute("delete from oxorderfiles where oxid='_testOrderFile'");
        parent::tearDown();
    }

    /**
     * Article_Files::Save() test case
     *
     * @return null
     */
    public function testSaveWithDefaultValues()
    {
        $this->setRequestParameter('editval', array('oxarticles__oxisdownloadable' => 1));
        $this->setRequestParameter('article_files', array("_testId" => "_testFile"));

        $fileDefaultProperties = array(
            'oxfiles__oxdownloadexptime' => -1,
            'oxfiles__oxlinkexptime' => -1,
            'oxfiles__oxmaxunregdownloads' => -1,
            'oxfiles__oxmaxdownloads' => -1,
        );

        $file = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('load', 'assign', 'save'));
        $file->expects($this->once())->method('load')->with('_testId');
        $file->expects($this->once())->method('assign')->with($fileDefaultProperties);
        $file->expects($this->once())->method('save');
        oxTestModules::addModuleObject('oxFile', $file);

        $articleFiles = oxNew('Article_Files');
        $articleFiles->save();
    }

    public function testSaveWithSetValues()
    {
        $fileProperties = array(
            'oxfiles__oxdownloadexptime' => 'oxdownloadexptime',
            'oxfiles__oxlinkexptime' => 'oxlinkexptime',
            'oxfiles__oxmaxunregdownloads' => 'oxmaxunregdownloads',
            'oxfiles__oxmaxdownloads' => 'oxmaxdownloads',
        );

        $this->setRequestParameter('editval', array('oxarticles__oxisdownloadable' => 1));
        $this->setRequestParameter('article_files', array( '_testId' => $fileProperties));

        $file = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('load', 'assign', 'save'));
        $file->expects($this->once())->method('load')->with('_testId');
        $file->expects($this->once())->method('assign')->with($fileProperties);
        $file->expects($this->once())->method('save');
        oxTestModules::addModuleObject('oxFile', $file);

        $articleFiles = oxNew('Article_Files');
        $articleFiles->save();
    }

    public function providerSaveDoNotSaveIfWrongFileName()
    {
        return array(
            array(array('oxfiles__oxfilename' => 'some__not_existing_file')),
            array(array('oxfiles__oxfilename' => '../../../config.inc.php'))
        );
    }

    /**
     * @param array $fileProperties
     *
     * @dataProvider providerSaveDoNotSaveIfWrongFileName
     */
    public function testSaveDoNotSaveIfWrongFileName($fileProperties)
    {
        $this->setRequestParameter("editval", array("oxarticles__oxisdownloadable" => 1));
        $this->setRequestParameter("article_files", array('_testId' => $fileProperties));

        $file = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('load', 'save'));
        $file->expects($this->once())->method('load');
        $file->expects($this->never())->method('save');
        oxTestModules::addModuleObject('oxFile', $file);

        $articleFiles = oxNew('Article_Files');
        $articleFiles->save();

        $errors = oxRegistry::getSession()->getVariable('Errors');

        if (!$errors) {
            $this->fail('Should set exception: file above download folder.');
        }

        $error = unserialize($errors['default'][0]);
        $this->assertEquals('Keine Dateien hochgeladen', $error->getOxMessage());
    }

    /**
     * Article_Files::getArticle() test case
     *
     * @return null
     */
    public function testGetArticle()
    {
        $this->setRequestParameter("oxid", 2000);

        $oView = oxNew('Article_Files');
        $this->assertEquals(2000, $oView->getArticle()->getId());
    }

    /**
     * Article_Files::getArticle() test case
     *
     * @return null
     */
    public function testGetArticleAlreadySet()
    {
        $this->setRequestParameter("oxid", 2000);
        $oView = $this->getProxyClass("Article_Files");
        $oView->setNonPublicVar("_oArticle", "_testArt");
        $this->assertEquals("_testArt", $oView->getArticle());
    }

    /**
     * Article_Files::deletefile() test case
     *
     * @return null
     */
    public function testDeletefile()
    {
        oxTestModules::addFunction('oxfile', '_deleteFile', '{ return true; }');
        $oDb = oxDb::getDb();
        $oDb->execute("insert into oxfiles set oxid='_testFileId', oxartid='2000'");
        $this->setRequestParameter("oxid", 2000);
        $this->setRequestParameter("fileid", "_testFileId");
        $oView = $this->getProxyClass("Article_Files");
        $oView->deletefile();
        $this->assertFalse($oDb->getOne("select oxid from oxfiles where oxid='_testFileId'"));
    }

    /**
     * Article_Files::deletefile() test case when demoShop = true
     *
     * @return null
     */
    public function testDeletefileDemoShop()
    {
        oxTestModules::addFunction('oxfile', '_deleteFile', '{ return true; }');
        $oDb = oxDb::getDb();
        $oDb->execute("insert into oxfiles set oxid='_testFileId', oxartid='2000'");
        $this->setRequestParameter("oxid", 2000);
        $this->setRequestParameter("fileid", "_testFileId");

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("isDemoShop"));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(true));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleFiles::class, array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->deletefile();

        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertEquals('ARTICLE_EXTEND_UPLOADISDISABLED', $oErr->getOxMessage());
    }

    /**
     * Article_Files::deletefile() test case
     *
     * @return null
     */
    public function testDeletefileDifferentArticle()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("insert into oxfiles set oxid='_testFileId', oxartid='2000'");
        $this->setRequestParameter("oxid", 1000);
        $this->setRequestParameter("fileid", "_testFileId");
        $oView = $this->getProxyClass("Article_Files");
        $oView->deletefile();
        $this->assertEquals('_testFileId', $oDb->getOne("select oxid from oxfiles where oxid='_testFileId'"));
    }

    /**
     * Article_Files::deletefile() test case
     *
     * @return null
     */
    public function testDeleteUsedFile()
    {
        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_orderId');
        $oOrder->save();

        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->setId('_orderArticleId');
        $oOrderArticle->oxorderarticles__oxorderid = new Field($oOrder->getId());
        $oOrderArticle->save();

        oxTestModules::addFunction('oxfile', '_deleteFile', '{ return true; }');
        $oDb = oxDb::getDb();


        $oDb->execute(
            'insert into oxorderfiles set oxid="_testOrderFile", oxfileid="_testFileId", oxmaxdownloadcount="10", oxlinkexpirationtime="24",
                            oxdownloadexpirationtime="12",  oxorderid = "_orderId", oxorderarticleid ="_orderarticleId", oxvaliduntil="2050-10-50 12:12:00", oxdownloadcount="2", oxfirstdownload="2011-10-10", oxlastdownload="2011-10-20"'
        );


        $oDb->execute("insert into oxfiles set oxid='_testFileId', oxartid='2000'");
        $this->setRequestParameter("oxid", 2000);
        $this->setRequestParameter("fileid", "_testFileId");
        $oView = $this->getProxyClass("Article_Files");
        $oView->deletefile();
        $this->assertEquals('_testFileId', $oDb->getOne("select oxid from oxfiles where oxid='_testFileId'"));
    }

    /**
     * Article_Files::upload() test case
     *
     * @return null
     */
    public function testUpload()
    {
        oxTestModules::addFunction('oxfile', 'processFile', '{ return true; }');
        oxTestModules::addFunction('oxfile', 'isUnderDownloadFolder', '{ return true; }');
        $oDb = oxDb::getDb();
        $this->setRequestParameter("oxid", '2000');
        $this->setRequestParameter("newfile", array("oxfiles__oxid" => "_testFileId", "oxfiles__oxpurchasedonly" => 1));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getUploadedFile", "isDemoShop"));
        $oConfig->expects($this->once())->method('getUploadedFile')->will($this->returnValue(array("name" => "testName")));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(false));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleFiles::class, array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->upload();

        $oFile = oxNew("oxFile");
        $oFile->load("_testFileId");
        $this->assertEquals(1, $oFile->oxfiles__oxpurchasedonly->value);
        $this->assertEquals('2000', $oFile->oxfiles__oxartid->value);
        $this->assertEquals("testName", $oFile->oxfiles__oxfilename->value);
    }

    /**
     * Article_Files::upload() test case when demoShop is true
     *
     * @return null
     */
    public function testUploadDemoShop()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("isDemoShop"));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(true));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleFiles::class, array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->upload();

        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertEquals('ARTICLE_EXTEND_UPLOADISDISABLED', $oErr->getOxMessage());
    }

    /**
     * Article_Files::upload() test case
     *
     * @return null
     */
    public function testUploadNoFile()
    {
        $oDb = oxDb::getDb();
        $this->setRequestParameter("oxid", '2000');
        $this->setRequestParameter("newfile", array("oxfiles__oxid" => "_testFileId", "oxfiles__oxpurchasedonly" => 1));
        $oView = $this->getProxyClass("Article_Files");
        $oView->upload();
        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertEquals('Keine Dateien hochgeladen', $oErr->getOxMessage());
    }

    /**
     * Article_Files::upload() test case
     *
     * @return null
     */
    public function testUploadNotProcessedFile()
    {
        $oDb = oxDb::getDb();
        $this->setRequestParameter("oxid", '2000');
        $this->setRequestParameter("newfile", array("oxfiles__oxid" => "_testFileId", "oxfiles__oxpurchasedonly" => 1));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getUploadedFile", "isDemoShop"));
        $oConfig->expects($this->once())->method('getUploadedFile')->will($this->returnValue(array("name" => "testName")));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(false));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleFiles::class, array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->upload();

        $this->setAdminMode(true);
        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertEquals('Keine Dateien hochgeladen', $oErr->getOxMessage());
    }

    public function testUploadExceptionIfAboveDownloadFolder()
    {
        $this->setRequestParameter("newfile", array('oxfiles__oxfilename' => '../../some_file_name'));

        $articleFiles = oxNew('article_files');
        $articleFiles->upload();

        $errors = oxRegistry::getSession()->getVariable('Errors');

        if (!$errors) {
            $this->fail('Should set exception: file above download folder.');
        }

        $error = unserialize($errors['default'][0]);
        $this->assertEquals('Keine Dateien hochgeladen', $error->getOxMessage());
    }

    /**
     * Test for Article_Files::_processOptions()
     *
     * @return null
     */
    public function testProcessOptions()
    {
        $aParams["oxfiles__oxdownloadexptime"] = "";
        $aParams["oxfiles__oxlinkexptime"] = "";
        $aParams["oxfiles__oxmaxunregdownloads"] = "";
        $aParams["oxfiles__oxmaxdownloads"] = "";
        $aResults["oxfiles__oxdownloadexptime"] = -1;
        $aResults["oxfiles__oxlinkexptime"] = -1;
        $aResults["oxfiles__oxmaxunregdownloads"] = -1;
        $aResults["oxfiles__oxmaxdownloads"] = -1;
        $oView = $this->getProxyClass("Article_Files");
        $this->assertEquals($aResults, $oView->UNITprocessOptions($aParams));
    }

    /**
     * Test for Article_Files::getConfigOptionValue()
     *
     * @return null
     */
    public function testGetConfigOptionValue()
    {
        $oView = $this->getProxyClass("Article_Files");
        $this->assertEquals("", $oView->getConfigOptionValue(-1));
        $this->assertEquals(0, $oView->getConfigOptionValue(0));
        $this->assertEquals(20, $oView->getConfigOptionValue(20));
    }
}
