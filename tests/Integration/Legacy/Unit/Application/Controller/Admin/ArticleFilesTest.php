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
class ArticleFilesTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $oDb = oxDb::getDb();
        $oDb->execute("delete from oxfiles where oxid='_testFileId'");
        $oDb->execute("delete from oxorderfiles where oxid='_testOrderFile'");
        parent::tearDown();
    }

    /**
     * Article_Files::Save() test case
     */
    public function testSaveWithDefaultValues()
    {
        $this->setRequestParameter('editval', ['oxarticles__oxisdownloadable' => 1]);
        $this->setRequestParameter('article_files', ["_testId" => "_testFile"]);

        $fileDefaultProperties = ['oxfiles__oxdownloadexptime' => -1, 'oxfiles__oxlinkexptime' => -1, 'oxfiles__oxmaxunregdownloads' => -1, 'oxfiles__oxmaxdownloads' => -1];

        $file = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, ['load', 'assign', 'save']);
        $file->expects($this->once())->method('load')->with('_testId');
        $file->expects($this->once())->method('assign')->with($fileDefaultProperties);
        $file->expects($this->once())->method('save');
        oxTestModules::addModuleObject('oxFile', $file);

        $articleFiles = oxNew('Article_Files');
        $articleFiles->save();
    }

    public function testSaveWithSetValues()
    {
        $fileProperties = ['oxfiles__oxdownloadexptime' => 'oxdownloadexptime', 'oxfiles__oxlinkexptime' => 'oxlinkexptime', 'oxfiles__oxmaxunregdownloads' => 'oxmaxunregdownloads', 'oxfiles__oxmaxdownloads' => 'oxmaxdownloads'];

        $this->setRequestParameter('editval', ['oxarticles__oxisdownloadable' => 1]);
        $this->setRequestParameter('article_files', ['_testId' => $fileProperties]);

        $file = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, ['load', 'assign', 'save']);
        $file->expects($this->once())->method('load')->with('_testId');
        $file->expects($this->once())->method('assign')->with($fileProperties);
        $file->expects($this->once())->method('save');
        oxTestModules::addModuleObject('oxFile', $file);

        $articleFiles = oxNew('Article_Files');
        $articleFiles->save();
    }

    public function providerSaveDoNotSaveIfWrongFileName(): \Iterator
    {
        yield [['oxfiles__oxfilename' => 'some__not_existing_file']];
        yield [['oxfiles__oxfilename' => '../../../config.inc.php']];
    }

    /**
     * @param array $fileProperties
     *
     * @dataProvider providerSaveDoNotSaveIfWrongFileName
     */
    public function testSaveDoNotSaveIfWrongFileName($fileProperties)
    {
        $this->setRequestParameter("editval", ["oxarticles__oxisdownloadable" => 1]);
        $this->setRequestParameter("article_files", ['_testId' => $fileProperties]);

        $file = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, ['load', 'save']);
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
        $this->assertSame('Keine Dateien hochgeladen', $error->getOxMessage());
    }

    /**
     * Article_Files::getArticle() test case
     */
    public function testGetArticle()
    {
        $this->setRequestParameter("oxid", 2000);

        $oView = oxNew('Article_Files');
        $this->assertSame(2000, $oView->getArticle()->getId());
    }

    /**
     * Article_Files::getArticle() test case
     */
    public function testGetArticleAlreadySet()
    {
        $this->setRequestParameter("oxid", 2000);
        $oView = $this->getProxyClass("Article_Files");
        $oView->setNonPublicVar("_oArticle", "_testArt");
        $this->assertSame("_testArt", $oView->getArticle());
    }

    /**
     * Article_Files::deletefile() test case
     */
    public function testDeletefile()
    {
        oxTestModules::addFunction('oxfile', 'deleteFile', '{ return true; }');
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
     */
    public function testDeletefileDemoShop()
    {
        oxTestModules::addFunction('oxfile', 'deleteFile', '{ return true; }');
        $oDb = oxDb::getDb();
        $oDb->execute("insert into oxfiles set oxid='_testFileId', oxartid='2000'");
        $this->setRequestParameter("oxid", 2000);
        $this->setRequestParameter("fileid", "_testFileId");

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isDemoShop"]);
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleFiles::class, ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->deletefile();

        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertSame('ARTICLE_EXTEND_UPLOADISDISABLED', $oErr->getOxMessage());
    }

    /**
     * Article_Files::deletefile() test case
     */
    public function testDeletefileDifferentArticle()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("insert into oxfiles set oxid='_testFileId', oxartid='2000'");
        $this->setRequestParameter("oxid", 1000);
        $this->setRequestParameter("fileid", "_testFileId");
        $oView = $this->getProxyClass("Article_Files");
        $oView->deletefile();
        $this->assertSame('_testFileId', $oDb->getOne("select oxid from oxfiles where oxid='_testFileId'"));
    }

    /**
     * Article_Files::deletefile() test case
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

        oxTestModules::addFunction('oxfile', 'deleteFile', '{ return true; }');
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
        $this->assertSame('_testFileId', $oDb->getOne("select oxid from oxfiles where oxid='_testFileId'"));
    }

    /**
     * Article_Files::upload() test case
     */
    public function testUpload()
    {
        oxTestModules::addFunction('oxfile', 'processFile', '{ return true; }');
        oxTestModules::addFunction('oxfile', 'isUnderDownloadFolder', '{ return true; }');
        oxDb::getDb();
        $this->setRequestParameter("oxid", '2000');
        $this->setRequestParameter("newfile", ["oxfiles__oxid" => "_testFileId", "oxfiles__oxpurchasedonly" => 1]);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getUploadedFile", "isDemoShop"]);
        $oConfig->expects($this->once())->method('getUploadedFile')->willReturn(["name" => "testName"]);
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(false);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleFiles::class, ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->upload();

        $oFile = oxNew("oxFile");
        $oFile->load("_testFileId");
        $this->assertSame(1, $oFile->oxfiles__oxpurchasedonly->value);
        $this->assertSame('2000', $oFile->oxfiles__oxartid->value);
        $this->assertSame("testName", $oFile->oxfiles__oxfilename->value);
    }

    /**
     * Article_Files::upload() test case when demoShop is true
     */
    public function testUploadDemoShop()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isDemoShop"]);
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleFiles::class, ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->upload();

        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertSame('ARTICLE_EXTEND_UPLOADISDISABLED', $oErr->getOxMessage());
    }

    /**
     * Article_Files::upload() test case
     */
    public function testUploadNoFile()
    {
        oxDb::getDb();
        $this->setRequestParameter("oxid", '2000');
        $this->setRequestParameter("newfile", ["oxfiles__oxid" => "_testFileId", "oxfiles__oxpurchasedonly" => 1]);
        $oView = $this->getProxyClass("Article_Files");
        $oView->upload();

        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertSame('Keine Dateien hochgeladen', $oErr->getOxMessage());
    }

    /**
     * Article_Files::upload() test case
     */
    public function testUploadNotProcessedFile()
    {
        $this->setRequestParameter("oxid", '2000');
        $this->setRequestParameter("newfile", ["oxfiles__oxid" => "_testFileId", "oxfiles__oxpurchasedonly" => 1]);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getUploadedFile", "isDemoShop"]);
        $oConfig->method('getUploadedFile')->willReturn(["name" => "testName"]);
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(false);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleFiles::class, ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->upload();

        $this->setAdminMode(true);
        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertSame('Keine Dateien hochgeladen', $oErr->getOxMessage());
    }

    public function testUploadExceptionIfAboveDownloadFolder()
    {
        $this->setRequestParameter("newfile", ['oxfiles__oxfilename' => '../../some_file_name']);

        $articleFiles = oxNew('article_files');
        $articleFiles->upload();

        $errors = oxRegistry::getSession()->getVariable('Errors');

        if (!$errors) {
            $this->fail('Should set exception: file above download folder.');
        }

        $error = unserialize($errors['default'][0]);
        $this->assertSame('Keine Dateien hochgeladen', $error->getOxMessage());
    }

    /**
     * Test for Article_Files::processOptions()
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
        $this->assertSame($aResults, $oView->processOptions($aParams));
    }

    /**
     * Test for Article_Files::getConfigOptionValue()
     */
    public function testGetConfigOptionValue()
    {
        $oView = $this->getProxyClass("Article_Files");
        $this->assertSame("", $oView->getConfigOptionValue(-1));
        $this->assertSame(0, $oView->getConfigOptionValue(0));
        $this->assertSame(20, $oView->getConfigOptionValue(20));
    }
}
