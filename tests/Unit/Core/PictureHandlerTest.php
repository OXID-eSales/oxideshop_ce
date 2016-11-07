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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Core;

use \oxField;
use \oxTestModules;

class PictureHandlerTest extends \OxidTestCase
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
        $oPicHandler = $this->getMock('oxPictureHandler', array('_getBaseMasterImageFileName'));
        $oPicHandler->expects($this->once())->method('_getBaseMasterImageFileName')->with($this->equalTo('testPic_p1.jpg'))->will($this->returnValue("testPic.jpg"));

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

        $this->assertEquals('testPic_p1.jpg', $oPicHandler->UNITgetBaseMasterImageFileName("testPic_p1.jpg"));
        $this->assertEquals('testPic2.jpg', $oPicHandler->UNITgetBaseMasterImageFileName("testPic2.jpg"));
        $this->assertEquals('testPic3.jpg', $oPicHandler->UNITgetBaseMasterImageFileName("bla/testPic3.jpg"));
    }

    /**
     * Testing deleting article master picture and all generated pictures
     */
    public function testDeleteArticleMasterPicture()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);

        $aDelPics = array();
        $aDelPics[] = array("sField"    => "oxpic1",
                            "sDir"      => "master/product/1/",
                            "sFileName" => "testPic1.jpg");

        $aDelPics[] = array("sField"    => "oxpic1",
                            "sDir"      => "master/product/icon/",
                            "sFileName" => "testIco1.jpg");

        $aDelPics[] = array("sField"    => "oxpic1",
                            "sDir"      => "master/product/thumb/",
                            "sFileName" => "testThumb1.jpg");

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("testPic1.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->at(0))->method('safePictureDelete')->with($this->equalTo($aDelPics[0]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[0]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[0]["sField"]))->will($this->returnValue(true));
        $oUtilsPic->expects($this->at(1))->method('safePictureDelete')->with($this->equalTo($aDelPics[1]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[1]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[1]["sField"]))->will($this->returnValue(true));
        $oUtilsPic->expects($this->at(2))->method('safePictureDelete')->with($this->equalTo($aDelPics[2]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[2]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[2]["sField"]))->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock('oxPictureHandler', array('getZoomName', 'getMainIconName', 'getThumbName', 'deleteZoomPicture'));
        $oPicHandler->expects($this->any())->method('getZoomName')->will($this->returnValue("testZoomPic1.jpg"));
        $oPicHandler->expects($this->any())->method('getMainIconName')->will($this->returnValue("testIco1.jpg"));
        $oPicHandler->expects($this->any())->method('getThumbName')->will($this->returnValue("testThumb1.jpg"));
        $oPicHandler->expects($this->any())->method('deleteZoomPicture')->will($this->returnValue(true));

        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, true);
    }

    /**
     * Testing deleting article master picture skips master picture
     */
    public function testDeleteArticleMasterPicture_skipsMasterPicture()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);

        $aDelPics[0] = array("sField"    => "oxpic1",
                             "sDir"      => "generated/product/1/",
                             "sFileName" => "testPic1.jpg");

        $aDelPics[1] = array("sField"    => "oxpic1",
                             "sDir"      => "generated/product/icon/",
                             "sFileName" => "testIco1.jpg");

        $aDelPics[2] = array("sField"    => "oxpic1",
                             "sDir"      => "generated/product/thumb/",
                             "sFileName" => "testThumb1.jpg");

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("testPic1.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->at(0))->method('safePictureDelete')->with($this->equalTo($aDelPics[0]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[0]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[0]["sField"]))->will($this->returnValue(true));
        $oUtilsPic->expects($this->at(1))->method('safePictureDelete')->with($this->equalTo($aDelPics[1]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[1]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[1]["sField"]));
        $oUtilsPic->expects($this->at(2))->method('safePictureDelete')->with($this->equalTo($aDelPics[2]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[2]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[2]["sField"]));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock('oxPictureHandler', array('deleteZoomPicture', 'getMainIconName', 'getThumbName'));
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

        $aDelPics[0] = array("sField"    => "oxpic1",
                             "sDir"      => "generated/product/1/",
                             "sFileName" => "testPic1.jpg");

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("testPic1.jpg");
        $oArticle->oxarticles__oxthumb = new oxField("testThumb1.jpg");
        $oArticle->oxarticles__oxicon = new oxField("testIco1.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->once())->method('safePictureDelete')->with($this->equalTo($aDelPics[0]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[0]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[0]["sField"]))->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock('oxPictureHandler', array('deleteZoomPicture', 'getMainIconName', 'getThumbName'));
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
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->any())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock('oxPictureHandler', array('deleteZoomPicture'));
        $oPicHandler->expects($this->once())->method('deleteZoomPicture')->with($this->isInstanceOf('OxidEsales\EshopCommunity\Application\Model\Article'), $this->equalTo(1));

        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, false);
    }

    /**
     * Testing deleting article master picture skips deleting if pic name is empty
     * or equal to 'nopic.jpg'
     */
    public function testDeleteArticleMasterPicture_emptyPic()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic1 = new oxField("nopic.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
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
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->at(0))->method('safePictureDelete')->with($this->equalTo($sFileName), $this->equalTo($sAbsImageDir . $sDir), $this->equalTo("oxarticles"), $this->equalTo($sField));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = $this->getMock('oxPictureHandler', array('getZoomName', 'getMainIconName', 'getThumbName'));
        $oPicHandler->expects($this->any())->method('getZoomName')->will($this->returnValue("testZoomPic1.jpg"));
        $oPicHandler->expects($this->any())->method('getMainIconName')->will($this->returnValue("testIco1.jpg"));
        $oPicHandler->expects($this->any())->method('getThumbName')->will($this->returnValue("testThumb1.jpg"));

        $oPicHandler->deleteArticleMasterPicture($oArticle, 1, true);
    }

    /**
     * Testing deleting article main icon
     */
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
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->exactly(1))->method('safePictureDelete');
        $oUtilsPic->expects($this->at(0))->method('safePictureDelete')->with($this->equalTo($sFileName), $this->equalTo($sAbsImageDir . $sDir), $this->equalTo("oxarticles"), $this->equalTo($sField));

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
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteMainIcon($oArticle);
    }

    /**
     * Testing deleting article thumbnail
     */
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
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->exactly(1))->method('safePictureDelete');
        $oUtilsPic->expects($this->at(0))->method('safePictureDelete')->with($this->equalTo($sFileName), $this->equalTo($sAbsImageDir . $sDir), $this->equalTo("oxarticles"), $this->equalTo($sField));

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
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteThumbnail($oArticle);
    }

    /**
     * Testing deleting article zoom picture when oxzoom field exist
     */
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
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->exactly(1))->method('safePictureDelete');
        $oUtilsPic->expects($this->at(0))->method('safePictureDelete')->with($this->equalTo($sFileName), $this->equalTo($sAbsImageDir . $sDir), $this->equalTo("oxarticles"), $this->equalTo($sField));

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
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
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
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->never())->method('safePictureDelete');

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteZoomPicture($oArticle, 1);
    }

    /**
     * Testing deleting article zoom picture - using master image as source
     */
    public function testDeleteZoomPicture_usingMasterImage()
    {
        $sAbsImageDir = $this->getConfig()->getPictureDir(false);
        oxTestModules::addFunction('oxDbMetaDataHandler', 'fieldExists', '{ return false; }');

        $aDelPics[0] = array("sField"    => "oxpic2",
                             "sDir"      => "z2/",
                             "sFileName" => "testMaster2.jpg");

        //test article
        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxpic2 = new oxField("testMaster2.jpg");

        // testing functions calls
        $oUtilsPic = $this->getMock('oxUtilsPic', array('safePictureDelete'));
        $oUtilsPic->expects($this->exactly(1))->method('safePictureDelete');
        $oUtilsPic->expects($this->at(0))->method('safePictureDelete')->with($this->equalTo($aDelPics[0]["sFileName"]), $this->equalTo($sAbsImageDir . $aDelPics[0]["sDir"]), $this->equalTo("oxarticles"), $this->equalTo($aDelPics[0]["sField"]));

        oxTestModules::addModuleObject('oxUtilsPic', $oUtilsPic);

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->deleteZoomPicture($oArticle, 2);
    }

    public function testGetImageSize()
    {
        $oPicHandler = oxNew('oxPictureHandler');

        $this->assertEquals(array(15, 153), $oPicHandler->getImageSize(array('asd' => '12*56', 'dsa' => '15*153'), 'dsa'));
        $this->assertEquals(null, $oPicHandler->getImageSize(array('asd' => '12*56', 'dsa' => '15*153'), 'dsas'));
        $this->assertEquals(null, $oPicHandler->getImageSize(array('asd' => '12*56', 'dsa' => '15*153')));
        $this->assertEquals(array(15, 153), $oPicHandler->getImageSize('15*153'));
        $this->assertEquals(array(15, 153), $oPicHandler->getImageSize('15*153', 'asd'));
        $this->assertEquals(null, $oPicHandler->getImageSize('15153'));
    }

    public function testGetPictureInfo()
    {
        $oCfg = $this->getMock('oxConfig', array('getPicturePath', 'getOutDir', 'getOutUrl'));
        $oCfg->expects($this->once())->method('getPicturePath')->will($this->returnValue('/qqq/pic/mic.jpg'));
        $oCfg->expects($this->once())->method('getOutDir')->will($this->returnValue('/qqq/'));
        $oCfg->expects($this->once())->method('getOutUrl')->will($this->returnValue('http://qqq/'));

        $cl = oxTestModules::publicize('oxPictureHandler', '_getPictureInfo');
        $oPicHandler = $this->getMock($cl, array('getConfig'));
        $oPicHandler->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        $this->assertEquals(array('path' => '/qqq/pic/mic.jpg', 'url' => 'http://qqq/pic/mic.jpg',), $oPicHandler->p_getPictureInfo('master/product/', 'nopic.jpg'));
    }

    public function testGetPictureInfoAltImgUrl()
    {
        $cl = oxTestModules::publicize('oxPictureHandler', '_getPictureInfo');
        $oPicHandler = $this->getMock($cl, array('getAltImageUrl'));
        $oPicHandler->expects($this->any())->method('getAltImageUrl')->will($this->returnValue('http://aqqa/master/product/nopic.jpg'));

        $this->assertEquals(array('path' => false, 'url' => 'http://aqqa/master/product/nopic.jpg',), $oPicHandler->p_getPictureInfo('master/product/', 'nopic.jpg'));
    }


    public function testGetPictureInfoAltImgUrlSsl()
    {
        $cl = oxTestModules::publicize('oxPictureHandler', '_getPictureInfo');
        $oPicHandler = $this->getMock($cl, array('getAltImageUrl'));
        $oPicHandler->expects($this->any())->method('getAltImageUrl')->will($this->returnValue('https://aqqa/master/product/nopic.jpg'));

        $this->assertEquals(array('path' => false, 'url' => 'https://aqqa/master/product/nopic.jpg',), $oPicHandler->p_getPictureInfo('master/product/', 'nopic.jpg'));
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
        $oCfg = $this->getMock('oxConfig', array('getPicturePath', 'getOutDir', 'getOutUrl'));
        $oCfg->expects($this->once())->method('getPicturePath')->will($this->returnValue(false));
        $oCfg->expects($this->never())->method('getOutDir');
        $oCfg->expects($this->never())->method('getOutUrl');

        $cl = oxTestModules::publicize('oxPictureHandler', '_getPictureInfo');
        $oPicHandler = $this->getMock($cl, array('getConfig'));
        $oPicHandler->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        $this->assertEquals(array('path' => false, 'url' => false,), $oPicHandler->p_getPictureInfo('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlNotSet()
    {
        $oCfg = $this->getMock('oxConfig', array('getConfigParam'));
        $oCfg->expects($this->any())->method('getConfigParam')->will($this->returnValue(false));

        $oPicHandler = $this->getMock('oxPictureHandler', array('getConfig'));
        $oPicHandler->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        $this->assertEquals(null, $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlAltUrl()
    {
        $oCfg = $this->getMock('oxConfig', array('getConfigParam'));
        $oCfg->expects($this->any())->method('getConfigParam')
            ->with($this->equalTo('sAltImageUrl'))
            ->will($this->returnValue('http://alt/image/url'));

        $oPicHandler = $this->getMock('oxPictureHandler', array('getConfig'));
        $oPicHandler->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $this->assertEquals('http://alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlSslAltUrlIsSsl()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->getMock('oxConfig', array('isSsl'));
        $config->expects($this->any())->method('isSsl')->will($this->returnValue(true));

        $config->setConfigParam('sAltImageUrl', 'http://alt/image/url');
        $config->setConfigParam('sSSLAltImageUrl', 'https://ssl-alt/image/url');

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->setConfig($config);

        $this->assertEquals('https://ssl-alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlSslAltUrlIsNotSsl()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->getMock('oxConfig', array('isSsl'));
        $config->expects($this->any())->method('isSsl')->will($this->returnValue(false));

        $config->setConfigParam('sAltImageUrl', 'http://alt/image/url');
        $config->setConfigParam('sSSLAltImageUrl', 'https://ssl-alt/image/url');

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->setConfig($config);

        $this->assertEquals('http://alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg'));
    }

    public function testGetAltImageUrlSslAltUrlForseSsl()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->getMock('oxConfig', array('isSsl'));
        $config->expects($this->any())->method('isSsl')->will($this->returnValue(false));

        $config->setConfigParam('sAltImageUrl', 'http://alt/image/url');
        $config->setConfigParam('sSSLAltImageUrl', 'https://ssl-alt/image/url');

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->setConfig($config);

        $this->assertEquals('https://ssl-alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg', true));
    }

    public function testGetAltImageUrlSslAltUrlForseNoSsl()
    {
        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->getMock('oxConfig', array('isSsl'));
        $config->expects($this->any())->method('isSsl')->will($this->returnValue(true));

        $config->setConfigParam('sAltImageUrl', 'http://alt/image/url');
        $config->setConfigParam('sSSLAltImageUrl', 'https://ssl-alt/image/url');

        $oPicHandler = oxNew('oxPictureHandler');
        $oPicHandler->setConfig($config);

        $this->assertEquals('http://alt/image/url/master/product/nopic.jpg', $oPicHandler->getAltImageUrl('master/product/', 'nopic.jpg', false));
    }

    /**
     * Picture url getter test
     *
     * @return null
     */
    public function testGetPicUrl()
    {
        $oPicHandler = $this->getMock('oxPictureHandler', array('_getPictureInfo'));
        $oPicHandler->expects($this->once())->method('_getPictureInfo')
            ->with($this->equalTo('master/product/'), $this->equalTo('nopic.jpg'))
            ->will($this->returnValue(array('url' => 'http://booo/master/product/nopic.jpg')));

        $this->assertEquals(
            'http://booo/generated/product/10_54_75/nopic.jpg',
            $oPicHandler->getPicUrl('product/', 'nopic.jpg', "10*54")
        );
    }

    /**
     * Picture url getter test
     *
     * @return null
     */
    public function testGetPicUrlNoSizeInfo()
    {
        $oPicHandler = $this->getMock('oxPictureHandler', array('getImageSize'));
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
     *
     * @return null
     */
    public function testGetPicUrlNoPathInfo()
    {
        $oPicHandler = $this->getMock('oxPictureHandler', array('getImageSize'));
        $oPicHandler->expects($this->never())->method('getImageSize');
        $this->assertNull($oPicHandler->getPicUrl(false, false, false));
    }

    /**
     * Product picture url getter test
     *
     * @return null
     */
    public function testGetProductPicUrl()
    {
        $oConfig = $this->getConfig();
        $sSize = $oConfig->getConfigParam('aDetailImageSizes');
        $sPath = $oConfig->getPictureUrl("") . 'generated/product/1/380_340_75/30-360-back_p1_z_f_th_665.jpg';

        $oPicHandler = oxNew('oxPictureHandler');
        $this->assertEquals($sPath, $oPicHandler->getProductPicUrl("product/1/", "30-360-back_p1_z_f_th_665.jpg", $sSize, "oxpic1"));
    }

    /**
     * Product picture url getter test
     *
     * @return null
     */
    public function testGetProductPicUrlNopic()
    {
        $oConfig = $this->getConfig();
        $sSize = $oConfig->getConfigParam('aDetailImageSizes');
        $sPath = $oConfig->getPictureUrl("") . 'generated/product/1/380_340_75/nopic.jpg';

        $oPicHandler = oxNew('oxPictureHandler');
        $this->assertEquals($sPath, $oPicHandler->getProductPicUrl("product/1/", false, $sSize, "oxpic1"));
    }
}
