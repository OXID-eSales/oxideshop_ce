<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxField;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\PictureHandler;
use OxidEsales\Eshop\Core\Registry;
use \oxTestModules;

final class PictureHandlerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing icon name getter
     */
    public function testGetIconName()
    {
        $oPicHandler = $this->getProxyClass('oxPictureHandler');

        $this->assertEquals('test.jpg', $oPicHandler->getIconName("test.jpg"));
        $this->assertEquals('test_p1.jpg', $oPicHandler->getIconName("test_p1.jpg"));
    }

    /**
     * Testing main icon name getter
     */
    public function testGetMainIconName()
    {
        $oPicHandler = $this->getMock(PictureHandler::class, ['getBaseMasterImageFileName']);
        $oPicHandler->expects($this->once())->method('getBaseMasterImageFileName')->with($this->equalTo('testPic_p1.jpg'))->will($this->returnValue("testPic.jpg"));

        $this->assertEquals('testPic.jpg', $oPicHandler->getMainIconName("testPic_p1.jpg"));
    }

    /**
     * Testing thumbnail name getter
     */
    public function testGetThumbName()
    {
        $oPicHandler = oxNew('oxPictureHandler');
        $this->assertEquals('testPic_p1.jpg', $oPicHandler->getThumbName("testPic_p1.jpg"));
    }

    /**
     * Testing zoom picture name getter
     */
    public function testGetZoomName()
    {
        $oPicHandler = oxNew('oxPictureHandler');
        $this->assertEquals('testPic_p1.jpg', $oPicHandler->getZoomName("testPic_p1.jpg", 1));
    }

    /**
     * Testing master image base name getter
     */
    public function testGetBaseMasterImageFileName()
    {
        $oPicHandler = $this->getProxyClass('oxPictureHandler');

        $this->assertEquals('testPic_p1.jpg', $oPicHandler->getBaseMasterImageFileName("testPic_p1.jpg"));
        $this->assertEquals('testPic2.jpg', $oPicHandler->getBaseMasterImageFileName("testPic2.jpg"));
        $this->assertEquals('testPic3.jpg', $oPicHandler->getBaseMasterImageFileName("bla/testPic3.jpg"));
    }

    /**
     * Testing deleting article master picture and all generated pictures
     */
    public function testDeleteArticleMasterPicture()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);

        $aDelPics = [];
        $aDelPics[] = ["sField"    => "oxpic1", "sDir"      => "master/product/1/", "sFileName" => "testPic1.jpg"];

        $aDelPics[] = ["sField"    => "oxpic1", "sDir"      => "master/product/icon/", "sFileName" => "testIco1.jpg"];

        $aDelPics[] = ["sField"    => "oxpic1", "sDir"      => "master/product/thumb/", "sFileName" => "testThumb1.jpg"];

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("testPic1.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic
            ->method('safePictureDelete')
            ->withConsecutive(
                [
                    $aDelPics[0]["sFileName"],
                    $sAbsImageDir . $aDelPics[0]["sDir"],
                    'oxarticles',
                    $aDelPics[0]["sField"]
                ],
                [
                    $aDelPics[1]["sFileName"],
                    $sAbsImageDir . $aDelPics[1]["sDir"],
                    'oxarticles',
                    $aDelPics[1]["sField"]
                ],
                [
                    $aDelPics[2]["sFileName"],
                    $sAbsImageDir . $aDelPics[2]["sDir"],
                    'oxarticles',
                    $aDelPics[2]["sField"]
                ]
            )
            ->willReturnOnConsecutiveCalls(
                true,
                true,
                true
            );

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock(PictureHandler::class, ['getZoomName', 'getMainIconName', 'getThumbName', 'deleteZoomPicture']);
        $oPicHandler->method('getZoomName')->will($this->returnValue("testZoomPic1.jpg"));
        $oPicHandler->method('getMainIconName')->will($this->returnValue("testIco1.jpg"));
        $oPicHandler->method('getThumbName')->will($this->returnValue("testThumb1.jpg"));
        $oPicHandler->method('deleteZoomPicture')->will($this->returnValue(true));

        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, true);
    }

    /**
     * Testing deleting article master picture skips master picture
     */
    public function testDeleteArticleMasterPicture_skipsMasterPicture()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);

        $aDelPics[0] = ["sField"    => "oxpic1", "sDir"      => "generated/product/1/", "sFileName" => "testPic1.jpg"];

        $aDelPics[1] = ["sField"    => "oxpic1", "sDir"      => "generated/product/icon/", "sFileName" => "testIco1.jpg"];

        $aDelPics[2] = ["sField"    => "oxpic1", "sDir"      => "generated/product/thumb/", "sFileName" => "testThumb1.jpg"];

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("testPic1.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic
            ->method('safePictureDelete')
            ->withConsecutive(
                [
                    $aDelPics[0]["sFileName"],
                    $sAbsImageDir . $aDelPics[0]["sDir"],
                    'oxarticles',
                    $aDelPics[0]["sField"]
                ],
                [
                    $aDelPics[1]["sFileName"],
                    $sAbsImageDir . $aDelPics[1]["sDir"],
                    'oxarticles',
                    $aDelPics[1]["sField"]
                ],
                [
                    $aDelPics[2]["sFileName"],
                    $sAbsImageDir . $aDelPics[2]["sDir"],
                    'oxarticles',
                    $aDelPics[2]["sField"]
                ]
            )
            ->willReturnOnConsecutiveCalls(
                true,
                null,
                null
            );
        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock(PictureHandler::class, ['deleteZoomPicture', 'getMainIconName', 'getThumbName']);
        $oPicHandler->expects($this->once())->method('deleteZoomPicture');
        $oPicHandler->expects($this->any())->method('getMainIconName')->will($this->returnValue("testIco1.jpg"));
        $oPicHandler->expects($this->any())->method('getThumbName')->will($this->returnValue("testThumb1.jpg"));

        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, false);
    }

    /**
     * Testing deleting article master picture skips thumbnail and main icon delete
     * if custom fields values are equal to generated values
     */
    public function testDeleteArticleMasterPicture_skipsIfDefinedCustomFields()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);

        $aDelPics[0] = ["sField"    => "oxpic1", "sDir"      => "generated/product/1/", "sFileName" => "testPic1.jpg"];

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("testPic1.jpg");
        $oArticle->oxarticles__oxthumb = new oxField("testThumb1.jpg");
        $oArticle->oxarticles__oxicon = new oxField("testIco1.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->once())->method('safePictureDelete')->with($this->equalTo($aDelPics[0]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[0]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[0]["sField"]))->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock(PictureHandler::class, ['deleteZoomPicture', 'getMainIconName', 'getThumbName']);
        $oPicHandler->expects($this->once())->method('deleteZoomPicture');
        $oPicHandler->expects($this->any())->method('getMainIconName')->will($this->returnValue("testIco1.jpg"));
        $oPicHandler->expects($this->any())->method('getThumbName')->will($this->returnValue("testThumb1.jpg"));

        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, false);
    }

    /**
     * Testing deleting article master picture - deletes custom oxzoom picture
     */
    public function testDeleteArticleMasterPicture_deletesCustomZoomPicture()
    {
        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("testPic1.jpg");
        $oArticle->oxarticles__oxzoom1 = new oxField("testCustomZoom.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->any())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock(PictureHandler::class, ['deleteZoomPicture']);
        $oPicHandler->expects($this->once())->method('deleteZoomPicture')->with($this->isInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class), $this->equalTo(1));

        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, false);
    }

    /**
     * Testing deleting article master picture skips deleting if pic name is empty
     * or equal to 'nopic.jpg'
     */
    public function testDeleteArticleMasterPicture_emptyPic()
    {
        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("nopic.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, false);

        $oArticle->oxarticles__oxpic1 = new oxField("");
        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, false);
    }

    /**
     * Testing deleting article master picture uses basename of master picture filename
     */
    public function testDeleteArticleMasterPicture_usesBasename()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);

        $sField = "oxpic1";
        $sDir = "master/product/1/";
        $sFileName = "testPic1.jpg";

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("1/testPic1.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->method('safePictureDelete')->with($this->equalTo($sFileName), $this->equalTo($sAbsImageDir . $sDir), $this->equalTo("oxarticles"), $this->equalTo($sField));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock(PictureHandler::class, ['getZoomName', 'getMainIconName', 'getThumbName']);
        $oPicHandler->expects($this->any())->method('getZoomName')->will($this->returnValue("testZoomPic1.jpg"));
        $oPicHandler->expects($this->any())->method('getMainIconName')->will($this->returnValue("testIco1.jpg"));
        $oPicHandler->expects($this->any())->method('getThumbName')->will($this->returnValue("testThumb1.jpg"));

        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, true);
    }

    public function testDeleteMainIcon()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);

        $sField = "oxicon";
        $sDir = "master/product/icon/";
        $sFileName = "testIcon.jpg";

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxicon = new oxField("testIcon.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->exactly(1))->method('safePictureDelete')->with($this->equalTo($sFileName), $this->equalTo($sAbsImageDir . $sDir), $this->equalTo("oxarticles"), $this->equalTo($sField));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteMainIcon($oArticle);
    }

    /**
     * Testing deleting article main icon - empty icon value
     */
    public function testDeleteMainIcon_emptyValue()
    {
        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxicon = new oxField("");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteMainIcon($oArticle);
    }

    public function testDeleteThumbnail()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);

        $sField = "oxthumb";
        $sDir = "master/product/thumb/";
        $sFileName = "testThumb.jpg";

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxthumb = new oxField("testThumb.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->exactly(1))->method('safePictureDelete')->with($this->equalTo($sFileName), $this->equalTo($sAbsImageDir . $sDir), $this->equalTo("oxarticles"), $this->equalTo($sField));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteThumbnail($oArticle);
    }

    /**
     * Testing deleting article thumbnail - empty icon value
     */
    public function testDeleteThumbnail_emptyValue()
    {
        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxthumb = new oxField("");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteThumbnail($oArticle);
    }

    public function testDeleteZoomPicture_dbFieldExists()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);
        oxTestModules::addFunction('oxDbMetaDataHandler', 'fieldExists', '{ return true; }');

        $sField = "oxzoom2";
        $sDir = "z2/";
        $sFileName = "testZoom2.jpg";

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxzoom2 = new oxField("testZoom2.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->exactly(1))->method('safePictureDelete')->with($this->equalTo($sFileName), $this->equalTo($sAbsImageDir . $sDir), $this->equalTo("oxarticles"), $this->equalTo($sField));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteZoomPicture($oArticle, 2);
    }

    /**
     * Testing deleting article zoom picture - empty icon value
     */
    public function testDeleteZoomPicture_emptyValue()
    {
        oxTestModules::addFunction('oxDbMetaDataHandler', 'fieldExists', '{ return true; }');

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxzoom1 = new oxField("");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteZoomPicture($oArticle, 1);
    }

    /**
     * Testing deleting article zoom picture - with nopic.jpg value
     */
    public function testDeleteZoomPicture_noPic()
    {
        oxTestModules::addFunction('oxDbMetaDataHandler', 'fieldExists', '{ return true; }');

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxzoom1 = new oxField("nopic.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteZoomPicture($oArticle, 1);
    }

    public function testDeleteZoomPicture_usingMasterImage()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);
        oxTestModules::addFunction('oxDbMetaDataHandler', 'fieldExists', '{ return false; }');

        $aDelPics[0] = ["sField"    => "oxpic2", "sDir"      => "z2/", "sFileName" => "testMaster2.jpg"];

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic2 = new oxField("testMaster2.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock(\OxidEsales\Eshop\Core\UtilsPic::class, ['safePictureDelete']);
        $oUtilsPic->expects($this->exactly(1))->method('safePictureDelete')->with($this->equalTo($aDelPics[0]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[0]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[0]["sField"]));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteZoomPicture($oArticle, 2);
    }

    public function testGetImageSize()
    {
        $oPicHandler = oxNew('oxPictureHandler');

        $this->assertEquals([15, 153], $oPicHandler->getImageSize(['asd' => '12*56', 'dsa' => '15*153'], 'dsa'));
        $this->assertEquals(null, $oPicHandler->getImageSize(['asd' => '12*56', 'dsa' => '15*153'], 'dsas'));
        $this->assertEquals(null, $oPicHandler->getImageSize(['asd' => '12*56', 'dsa' => '15*153']));
        $this->assertEquals([15, 153], $oPicHandler->getImageSize('15*153'));
        $this->assertEquals([15, 153], $oPicHandler->getImageSize('15*153', 'asd'));
        $this->assertEquals(null, $oPicHandler->getImageSize('15153'));
    }

    public function testGetPictureInfo()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getPicturePath', 'getOutDir', 'getOutUrl']);
        $oCfg->expects($this->once())->method('getPicturePath')->will($this->returnValue('/qqq/pic/mic.jpg'));
        $oCfg->expects($this->once())->method('getOutDir')->will($this->returnValue('/qqq/'));
        $oCfg->expects($this->once())->method('getOutUrl')->will($this->returnValue('http://qqq/'));

        $oPicHandler = $this->getMock('oxPictureHandler', ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);

        $this->assertEquals(['path' => '/qqq/pic/mic.jpg', 'url' => 'http://qqq/pic/mic.jpg'], $oPicHandler->getPictureInfo('master/product/', 'nopic.jpg'));
    }

    public function testGetPictureInfoAltImgUrl()
    {
        $oPicHandler = $this->getMock('oxPictureHandler', ['getAltImageUrl']);
        $oPicHandler->expects($this->any())->method('getAltImageUrl')->will($this->returnValue('http://aqqa/master/product/nopic.jpg'));

        $this->assertEquals(['path' => false, 'url' => 'http://aqqa/master/product/nopic.jpg'], $oPicHandler->getPictureInfo('master/product/', 'nopic.jpg'));
    }


    public function testGetPictureInfoAltImgUrlSsl()
    {
        $oPicHandler = $this->getMock('oxPictureHandler', ['getAltImageUrl']);
        $oPicHandler->expects($this->any())->method('getAltImageUrl')->will($this->returnValue('https://aqqa/master/product/nopic.jpg'));

        $this->assertEquals(['path' => false, 'url' => 'https://aqqa/master/product/nopic.jpg'], $oPicHandler->getPictureInfo('master/product/', 'nopic.jpg'));
    }

    /**
     * #5720
     */
    public function testGetAltImageUrlNoDoubleSlashes()
    {
        $oPicHandler = oxnew('oxPictureHandler');

        $this->setConfigParam('sAltImageUrl', 'https://example.com');
        $this->assertEquals('https://example.com/path/nopic.jpg', $oPicHandler->getAltImageUrl('path/', 'nopic.jpg'));

        $this->setConfigParam('sAltImageUrl', 'https://example.com/');
        $this->assertEquals('https://example.com/path/nopic.jpg', $oPicHandler->getAltImageUrl('path/', 'nopic.jpg'));
    }

    public function testGetPictureInfoNotFound()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getPicturePath', 'getOutDir', 'getOutUrl']);
        $oCfg->expects($this->once())->method('getPicturePath')->will($this->returnValue(false));
        $oCfg->expects($this->never())->method('getOutDir');
        $oCfg->expects($this->never())->method('getOutUrl');

        $oPicHandler = $this->getMock('oxPictureHandler', ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);

        $this->assertEquals(['path' => false, 'url' => false], $oPicHandler->getPictureInfo('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlNotSet()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oCfg->expects($this->any())->method('getConfigParam')->will($this->returnValue(false));

        $oPicHandler = $this->getMock(PictureHandler::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);

        $this->assertEquals(null, $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlAltUrl()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oCfg->expects($this->any())->method('getConfigParam')
            ->with($this->equalTo('sAltImageUrl'))
            ->will($this->returnValue('http://alt/image/url'));

        $oPicHandler = $this->getMock(PictureHandler::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);

        $this->assertEquals('http://alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlSslAltUrlIsSsl()
    {
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $config */
        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['isSsl']);
        $config->expects($this->any())->method('isSsl')->will($this->returnValue(true));

        $config->setConfigParam('sAltImageUrl', 'http://alt/image/url');
        $config->setConfigParam('sSSLAltImageUrl', 'https://ssl-alt/image/url');

        $oPicHandler = oxNew('oxPictureHandler');
        Registry::set(Config::class, $config);

        $this->assertEquals('https://ssl-alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlSslAltUrlIsNotSsl()
    {
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $config */
        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['isSsl']);
        $config->expects($this->any())->method('isSsl')->will($this->returnValue(false));

        $config->setConfigParam('sAltImageUrl', 'http://alt/image/url');
        $config->setConfigParam('sSSLAltImageUrl', 'https://ssl-alt/image/url');

        $oPicHandler = oxNew('oxPictureHandler');
        Registry::set(Config::class, $config);

        $this->assertEquals('http://alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlSslAltUrlForseSsl()
    {
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $config */
        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['isSsl']);
        $config->expects($this->any())->method('isSsl')->will($this->returnValue(false));

        $config->setConfigParam('sAltImageUrl', 'http://alt/image/url');
        $config->setConfigParam('sSSLAltImageUrl', 'https://ssl-alt/image/url');

        $oPicHandler = oxNew('oxPictureHandler');
        Registry::set(Config::class, $config);

        $this->assertEquals('https://ssl-alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg', true));
    }

    public function testGetAltImageUrlSslAltUrlForseNoSsl()
    {
        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $config */
        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['isSsl']);
        $config->expects($this->any())->method('isSsl')->will($this->returnValue(true));

        $config->setConfigParam('sAltImageUrl', 'http://alt/image/url');
        $config->setConfigParam('sSSLAltImageUrl', 'https://ssl-alt/image/url');

        $oPicHandler = oxNew('oxPictureHandler');
        Registry::set(Config::class, $config);

        $this->assertEquals('http://alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg', false));
    }

    /**
     * Picture url getter test
     */
    public function testGetPicUrl()
    {
        $oPicHandler = $this->getMock(PictureHandler::class, ['getPictureInfo']);
        $oPicHandler->expects($this->once())->method('getPictureInfo')
            ->with($this->equalTo('master/product/'), $this->equalTo('nopic.jpg'))
            ->will($this->returnValue(['url' => 'http://booo/master/product/nopic.jpg']));

        $this->assertEquals(
            'http://booo/generated/product/10_54_75/nopic.jpg',
            $oPicHandler->getPicUrl('product/', 'nopic.jpg', "10*54")
        );
    }

    public function testGetPicUrlConvertedWebp(): void
    {
        $this->setConfigParam('blConvertImagesToWebP', 1);

        $oPicHandler = $this->getMock(PictureHandler::class, ['getPictureInfo']);
        $oPicHandler->expects($this->once())->method('getPictureInfo')
            ->with($this->equalTo('master/product/'), $this->equalTo('picture.jpg'))
            ->will($this->returnValue(['url' => 'http://booo/master/product/picture.jpg.webp']));

        $this->assertEquals(
            'http://booo/generated/product/10_54_75/picture.jpg.webp',
            $oPicHandler->getPicUrl('product/', 'picture.jpg', "10*54")
        );
    }

    /**
     * Picture url getter test
     */
    public function testGetPicUrlNoSizeInfo()
    {
        $oPicHandler = $this->getMock(PictureHandler::class, ['getImageSize']);
        $oPicHandler->expects($this->once())->method('getImageSize')
            ->with(
                $this->equalTo(""),
                $this->equalTo(null)
            )
            ->will($this->returnValue(false));
        $this->assertNull($oPicHandler->getPicUrl('product/', 'nopic.jpg', ""));
    }

    /**
     * Picture url getter test
     */
    public function testGetPicUrlNoPathInfo()
    {
        $oPicHandler = $this->getMock(PictureHandler::class, ['getImageSize']);
        $oPicHandler->expects($this->never())->method('getImageSize');
        $this->assertNull($oPicHandler->getPicUrl(false, false, false));
    }

    /**
     * Product picture url getter test
     *
     * @dataProvider getProductPicUrlDataProvider
     */
    public function testGetProductPicUrl(string $filename, string $expectedFilename, bool $convertToWebP):void {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blConvertImagesToWebP', $convertToWebP);

        $sSize = $oConfig->getConfigParam('aDetailImageSizes');
        $sPath = $oConfig->getPictureUrl("") . 'generated/product/1/250_200_75/' . $expectedFilename;

        $oPicHandler = oxNew('oxPictureHandler');
        $this->assertEquals($sPath, $oPicHandler->getProductPicUrl("product/1/", $filename, $sSize, "oxpic1"));
    }

    public function getProductPicUrlDataProvider(): array
    {
        return [
            ['30-360-back_p1_z_f_th_665.jpg', '30-360-back_p1_z_f_th_665.jpg', false],
            ['30-360-back_p1_z_f_th_665.jpg', '30-360-back_p1_z_f_th_665.jpg.webp', true],
        ];
    }

    /**
     * Product picture url getter test
     *
     * @dataProvider getProductPicUrlNopicDataProvider
     */
    public function testGetProductPicUrlNopic(string $filename, bool $convertToWebP): void
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blConvertImagesToWebP', $convertToWebP);

        $sSize = $oConfig->getConfigParam('aDetailImageSizes');
        $sPath = $oConfig->getPictureUrl("") . 'generated/product/1/250_200_75/' . $filename;

        $oPicHandler = oxNew('oxPictureHandler');
        $this->assertEquals($sPath, $oPicHandler->getProductPicUrl("product/1/", false, $sSize, "oxpic1"));
    }

    public function getProductPicUrlNopicDataProvider(): array
    {
        return [
            ['nopic.jpg', false],
            ['nopic.webp', true],
        ];
    }
}
