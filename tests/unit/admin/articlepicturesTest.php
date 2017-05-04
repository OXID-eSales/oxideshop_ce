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

/**
 * Tests for Article_Pictures class
 */
class Unit_Admin_ArticlePicturesTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_oArticle = new oxArticle();
        $this->_oArticle->setId("_testArtId");
        $this->_oArticle->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }

    /**
     * Article_Pictures::save() test case
     *
     * @return null
     */
    public function testSaveAdditionalTest()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ return true; }');
        modConfig::getInstance()->setConfigParam('iPicCount', 0);

        $oView = $this->getMock("Article_Pictures", array("resetContentCache"));
        $oView->expects($this->once())->method('resetContentCache');

        $iCnt = 7;
        modConfig::getInstance()->setConfigParam('iPicCount', $iCnt);

        $oView->save();
    }

    /**
     * Article_Pictures::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        modConfig::setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxarticles where oxparentid != ''"));

        // testing..
        $oView = new Article_Pictures();
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof oxArticle);
        $this->assertTrue($aViewData["parentarticle"] instanceof oxArticle);

        $this->assertEquals('article_pictures.tpl', $sTplName);
    }

    /**
     * Article_Pictures::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        return;

        $myConfig = oxRegistry::getConfig();

        $sAbsDynImageDir = $myConfig->getPictureDir(false);
        $sActShopId = $myConfig->getBaseShopId();
        modSession::getInstance()->setVar("actshop", $sActShopId);

        $aTestData[0][1] = 'oxarticles';
        $aTestData[0][2] = 'oxthumb';
        $aTestData[0][3] = 'TH';
        $aTestData[0][4] = '0';
        $aTestData[0][5] = array("oxarticles__oxshopid" => $sActShopId);
        $aTestData[0][6] = $sAbsDynImageDir;

        $aTestData[1][1] = 'oxarticles';
        $aTestData[1][2] = 'oxicon';
        $aTestData[1][3] = 'ICO';
        $aTestData[1][4] = 'icon';
        $aTestData[1][5] = array("oxarticles__oxshopid" => $sActShopId);
        $aTestData[1][6] = $sAbsDynImageDir;

        for ($i = 1; $i <= $myConfig->getConfigParam('iPicCount'); $i++) {
            $iCnt = count($aTestData);

            $aTestData[$iCnt][1] = 'oxarticles';
            $aTestData[$iCnt][2] = 'oxpic' . $i;
            $aTestData[$iCnt][3] = 'P' . $i;
            $aTestData[$iCnt][4] = $i;
            $aTestData[$iCnt][5] = array("oxarticles__oxshopid" => $sActShopId);
            $aTestData[$iCnt][6] = $sAbsDynImageDir;
        }

        for ($i = 1; $i <= $myConfig->getConfigParam('iZoomPicCount'); $i++) {
            $iCnt = count($aTestData);

            $aTestData[$iCnt][1] = 'oxarticles';
            $aTestData[$iCnt][2] = 'oxzoom' . $i;
            $aTestData[$iCnt][3] = 'Z' . $i;
            $aTestData[$iCnt][4] = 'z' . $i;
            $aTestData[$iCnt][5] = array("oxarticles__oxshopid" => $sActShopId);
            $aTestData[$iCnt][6] = $sAbsDynImageDir;
        }

        oxTestModules::addFunction('oxarticle', 'load', '{ return true; }');
        oxTestModules::addFunction('oxarticle', 'save', '{ return true; }');
        oxTestModules::addFunction('oxarticle', 'assign', '{ return true; }');

        $sFnc = '{
                     if ( !isset( $this->_aTestData ) ) {
                         $this->_aTestData = array();
                     }
                     $iCnt = count( $this->_aTestData );
                     $this->_aTestData[$iCnt][1] = $aA[1];
                     $this->_aTestData[$iCnt][2] = $aA[2];
                     $this->_aTestData[$iCnt][3] = $aA[3];
                     $this->_aTestData[$iCnt][4] = $aA[4];
                     $this->_aTestData[$iCnt][5] = $aA[5];
                     $this->_aTestData[$iCnt][6] = $aA[6];
                 }';
        oxTestModules::addFunction('oxUtilsPic', 'overwritePic', $sFnc);
        oxTestModules::addFunction('oxUtilsPic', 'getTestData', '{ return $this->_aTestData; }');
        oxTestModules::addFunction('oxUtilsFile', 'processFiles', '{ return $aA[0]; }');

        $oView = new Article_Pictures();
        $oView->save();

        $myUtilsPic = oxRegistry::get("oxUtilsPic");
        $this->assertEquals($aTestData, $myUtilsPic->getTestData());
    }

    /**
     * Article_Pictures::deletePicture() test case - deleting icon
     *
     * @return null
     */
    public function testDeletePicture_deletingIcon()
    {
        modConfig::setRequestParameter("oxid", "_testArtId");
        modConfig::setRequestParameter("masterPicIndex", "ICO");

        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $oArtPic = $this->getMock("Article_Pictures", array("_deleteThumbnail", "_deleteMasterPicture"));
        $oArtPic->expects($this->never())->method('_deleteThumbnail');
        $oArtPic->expects($this->never())->method('_deleteMasterPicture');

        $this->_oArticle->oxarticles__oxicon = new oxField("testIcon.jpg");
        $this->_oArticle->save();

        $oArtPic->deletePicture();

        $this->assertEquals("", $oDb->getOne("select oxicon from oxarticles where oxid='_testArtId' "));
    }

    /**
     * Article_Pictures::deletePicture() test case - deleting thumbnail
     *
     * @return null
     */
    public function testDeletePicture_deletingThumbnail()
    {
        modConfig::setRequestParameter("oxid", "_testArtId");
        modConfig::setRequestParameter("masterPicIndex", "TH");
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $oArtPic = $this->getMock("Article_Pictures", array("_deleteMainIcon", "_deleteMasterPicture"));
        $oArtPic->expects($this->never())->method('_deleteMainIcon');
        $oArtPic->expects($this->never())->method('_deleteMasterPicture');

        $this->_oArticle->oxarticles__oxthumb = new oxField("testThumb.jpg");
        $this->_oArticle->save();

        $oArtPic->deletePicture();

        $this->assertEquals("", $oDb->getOne("select oxthumb from oxarticles where oxid='_testArtId' "));
    }

    /**
     * Article_Pictures::deletePicture() test case - deleting master picture
     *
     * @return null
     */
    public function testDeletePicture_deletingMasterPic()
    {
        modConfig::setRequestParameter("oxid", "_testArtId");
        modConfig::setRequestParameter("masterPicIndex", "2");
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $oArtPic = $this->getMock("Article_Pictures", array("_deleteMainIcon", "_deleteThumbnail"));
        $oArtPic->expects($this->never())->method('_deleteMainIcon');
        $oArtPic->expects($this->never())->method('_deleteThumbnail');

        $this->_oArticle->oxarticles__oxpic2 = new oxField("testPic2.jpg");
        $this->_oArticle->save();

        $oArtPic->deletePicture();

        $this->assertEquals("", $oDb->getOne("select oxpic2 from oxarticles where oxid='_testArtId' "));
    }

    /**
     * Article_Pictures::deletePicture() test case - updating amount of genereated pictures
     *
     * @return null
     */
    public function testDeletePicture_generatedPicsCounterReset()
    {
        modConfig::setRequestParameter("oxid", "_testArtId");
        modConfig::setRequestParameter("masterPicIndex", "2");

        $oArtPic = $this->getMock("Article_Pictures", array("_resetMasterPicture"));
        $oArtPic->expects($this->once())->method('_resetMasterPicture');

        $this->_oArticle->save();

        $oArtPic->deletePicture();

        $this->_oArticle->load("_testArtId");
    }

    /**
     * Article_Pictures::deleteMasterPicture()
     *
     * @return null
     */
    /*public function testDeleteMasterPicture()
    {
        $this->_oArticle->oxarticles__oxpic2 = new oxField( "testPic2.jpg" );
        $this->_oArticle->save();

        $oPicHandler = $this->getMock( "oxPictureHandler", array( "deleteArticleMasterPicture" ) );
        $oPicHandler->expects( $this->once() )->method( 'deleteArticleMasterPicture' ) ;

        modInstances::addMod( "oxPictureHandler", $oPicHandler );

        $oArtPic = $this->getProxyClass( "Article_Pictures" );
        $oArtPic->UNITdeleteMasterPicture( $this->_oArticle, 2 );

        $this->assertEquals( "", $this->_oArticle->oxarticles__oxpic2->value );
    }*/

    /**
     * Article_Pictures::deleteMasterPicture() - calling cleanup method if
     * field index = 1
     *
     * @return null
     */
    /*public function testDeleteMasterPicture_makesCleanupOnFields()
    {
        $this->_oArticle->oxarticles__oxpic1 = new oxField( "testPic1.jpg" );
        $this->_oArticle->oxarticles__oxpic2 = new oxField( "testPic2.jpg" );
        $this->_oArticle->save();

        $oPicHandler = $this->getMock( "oxPictureHandler", array( "deleteArticleMasterPicture" ) );
        $oPicHandler->expects( $this->exactly( 2 ) )->method( 'deleteArticleMasterPicture' ) ;

        modInstances::addMod( "oxPictureHandler", $oPicHandler );

        $oArtPic = $this->getMock( "Article_Pictures", array( "_cleanupCustomFields" ) );
        $oArtPic->expects( $this->never() )->method( '_cleanupCustomFields' );
        $oArtPic->UNITdeleteMasterPicture( $this->_oArticle, 2 );

        $oArtPic = $this->getMock( "Article_Pictures", array( "_cleanupCustomFields" ) );
        $oArtPic->expects( $this->once() )->method( '_cleanupCustomFields' );
        $oArtPic->UNITdeleteMasterPicture( $this->_oArticle, 1 );
    }*/

    /**
     * Article_Pictures::_deleteMainIcon()
     *
     * @return null
     */
    public function testDeleteMainIcon()
    {
        $oArticle = $this->getMock("oxArticle", array('isDerived'));
        $oArticle->expects($this->once())->method('isDerived')->will($this->returnValue(null));

        $oArticle->oxarticles__oxicon = new oxField("testIcon.jpg");

        $oPicHandler = $this->getMock("oxPictureHandler", array("deleteMainIcon"));
        $oPicHandler->expects($this->once())->method('deleteMainIcon');

        oxTestModules::addModuleObject("oxPictureHandler", $oPicHandler);

        $oArtPic = $this->getProxyClass("Article_Pictures");
        $oArtPic->UNITdeleteMainIcon($oArticle);

        $this->assertEquals("", $oArticle->oxarticles__oxicon->value);
    }

    /**
     * Article_Pictures::_deleteThumbnail()
     *
     * @return null
     */
    public function testDeleteThumbnail()
    {
        $oArticle = $this->getMock("oxArticle", array('isDerived'));
        $oArticle->expects($this->once())->method('isDerived')->will($this->returnValue(null));

        $oArticle->oxarticles__oxthumb = new oxField("testThumb.jpg");

        $oPicHandler = $this->getMock("oxPictureHandler", array("deleteThumbnail"));
        $oPicHandler->expects($this->once())->method('deleteThumbnail');

        oxTestModules::addModuleObject("oxPictureHandler", $oPicHandler);

        $oArtPic = $this->getProxyClass("Article_Pictures");
        $oArtPic->UNITdeleteThumbnail($oArticle);

        $this->assertEquals("", $oArticle->oxarticles__oxthumb->value);
    }

    /**
     * Article_Pictures::_resetMasterPicture()
     *
     * @return null
     */
    public function testResetMasterPicture()
    {
        $oArticle = $this->getMock("oxArticle", array('isDerived'));
        $oArticle->expects($this->once())->method('isDerived')->will($this->returnValue(null));

        $oArticle->oxarticles__oxpic2 = new oxField("testPic2.jpg");

        $oPicHandler = $this->getMock("oxPictureHandler", array("deleteArticleMasterPicture"));
        $oPicHandler->expects($this->once())->method('deleteArticleMasterPicture')->with($this->equalTo($oArticle), $this->equalTo(2), $this->equalTo(false));

        oxTestModules::addModuleObject("oxPictureHandler", $oPicHandler);

        $oArtPic = $this->getProxyClass("Article_Pictures");
        $oArtPic->UNITresetMasterPicture($oArticle, 2);

        $this->assertEquals("testPic2.jpg", $oArticle->oxarticles__oxpic2->value);
    }


    /**
     * Article_Pictures::_resetMasterPicture() - calling cleanup when field
     * index = 1
     *
     * @return null
     */
    public function testResetMasterPicture_makesCleanupOnFields()
    {
        $oArticle = $this->getMock("oxArticle", array('isDerived'));
        $oArticle->expects($this->any())->method('isDerived')->will($this->returnValue(null));

        $oArticle->oxarticles__oxpic1 = new oxField("testPic1.jpg");
        $oArticle->oxarticles__oxpic2 = new oxField("testPic2.jpg");

        $oPicHandler = $this->getMock("oxPictureHandler", array("deleteArticleMasterPicture"));
        $oPicHandler->expects($this->exactly(2))->method('deleteArticleMasterPicture');

        oxTestModules::addModuleObject("oxPictureHandler", $oPicHandler);

        $oArtPic = $this->getMock("Article_Pictures", array("_cleanupCustomFields"));
        $oArtPic->expects($this->never())->method('_cleanupCustomFields');
        $oArtPic->UNITresetMasterPicture($oArticle, 2);

        $oArtPic = $this->getMock("Article_Pictures", array("_cleanupCustomFields"));
        $oArtPic->expects($this->once())->method('_cleanupCustomFields');
        $oArtPic->UNITresetMasterPicture($oArticle, 1);
    }

    /**
     * Article_Pictures::_updateGeneratedPicsAmount()
     *
     * @return null
     */
    /*public function testUpdateGeneratedPicsAmount()
    {
        $this->_oArticle->oxarticles__oxpicsgenerated = new oxField( "5" );
        $this->_oArticle->save();

        $oArtPic = $this->getMock( "Article_Pictures", array( "_getMinUploadedMasterPicIndex" ) );
        $oArtPic->expects( $this->once() )->method( '_getMinUploadedMasterPicIndex' )->will( $this->returnValue( 3 ) );

        $oArtPic->UNITupdateGeneratedPicsAmount( $this->_oArticle );

        $oArticle = new oxArticle();
        $oArticle->load( "_testArtId" );
        $this->assertEquals( "2", $oArticle->oxarticles__oxpicsgenerated->value );
    }*/

    /**
     * Article_Pictures::_updateGeneratedPicsAmount() - when uploaded image
     * index is greater then already generated
     *
     * @return null
     */
    /*public function testUpdateGeneratedPicsAmount_withGreaterIndex()
    {
        $this->_oArticle->oxarticles__oxpicsgenerated = new oxField( "5" );
        $this->_oArticle->save();

        $oArtPic = $this->getMock( "Article_Pictures", array( "_getMinUploadedMasterPicIndex" ) );
        $oArtPic->expects( $this->once() )->method( '_getMinUploadedMasterPicIndex' )->will( $this->returnValue( 6 ) );

        $oArtPic->UNITupdateGeneratedPicsAmount( $this->_oArticle );

        $oArticle = new oxArticle();
        $oArticle->load( "_testArtId" );
        $this->assertEquals( "5", $oArticle->oxarticles__oxpicsgenerated->value );
    }*/

    /**
     * Article_Pictures::_cleanupZoomFields()
     *
     * @return null
     */
    /*public function testCleanupZoomFields()
    {
        $this->_oArticle->oxarticles__oxzoom2 = new oxField( "zoom2.jpg" );
        $this->_oArticle->oxarticles__oxzoom3 = new oxField( "zoom3.jpg" );

        $_FILES['myfile']['name']["M2"] = "value1";
        $_FILES['myfile']['name']["M3"] = "value2";

        $oArtPic = $this->getMock( "Article_Pictures", array( "_getUploadedMasterPicIndex" ) );
        $oArtPic->expects( $this->at( 0 ) )->method( '_getUploadedMasterPicIndex' )->with( $this->equalTo( "M2" ) )->will( $this->returnValue( 2 ) );
        $oArtPic->expects( $this->at( 1 ) )->method( '_getUploadedMasterPicIndex' )->with( $this->equalTo( "M3" ) )->will( $this->returnValue( 3 ) );

        $oArtPic->UNITcleanupZoomFields( $this->_oArticle );

        $this->assertEquals( "", $this->_oArticle->oxarticles__oxzoom2->value );
        $this->assertEquals( "", $this->_oArticle->oxarticles__oxzoom3->value );
    }*/

    /**
     * Article_Pictures::_cleanupCustomFields()
     *
     * @return null
     */
    public function testCleanupCustomFields()
    {
        $this->_oArticle->oxarticles__oxicon = new oxField("nopic.jpg");
        $this->_oArticle->oxarticles__oxthumb = new oxField("nopic.jpg");

        $_FILES['myfile']['name']["M2"] = "value1";
        $_FILES['myfile']['name']["M3"] = "value2";

        $oArtPic = $this->getProxyClass("Article_Pictures");

        $oArtPic->UNITcleanupCustomFields($this->_oArticle);

        $this->assertEquals("", $this->_oArticle->oxarticles__oxicon->value);
        $this->assertEquals("", $this->_oArticle->oxarticles__oxthumb->value);
    }

    /**
     * Article_Pictures::_cleanupCustomFields() - when custom fields are not empty
     *
     * @return null
     */
    public function testCleanupCustomFields_fieldsNotEmpty()
    {
        $this->_oArticle->oxarticles__oxicon = new oxField("testIcon.jpg");
        $this->_oArticle->oxarticles__oxthumb = new oxField("testThumb.jpg");

        $_FILES['myfile']['name']["M2"] = "value1";
        $_FILES['myfile']['name']["M3"] = "value2";

        $oArtPic = $this->getProxyClass("Article_Pictures");

        $oArtPic->UNITcleanupCustomFields($this->_oArticle);

        $this->assertEquals("testIcon.jpg", $this->_oArticle->oxarticles__oxicon->value);
        $this->assertEquals("testThumb.jpg", $this->_oArticle->oxarticles__oxthumb->value);
    }

    /**
     * Article_Pictures::_getUploadedMasterPicIndex()
     *
     * @return null
     */
    /*public function testGetUploadedMasterPicIndex()
    {
        $oArtPic = $this->getProxyClass( "Article_Pictures" );

        $this->assertEquals( "2", $oArtPic->UNITgetUploadedMasterPicIndex( "M2" ) );
        $this->assertEquals( "7", $oArtPic->UNITgetUploadedMasterPicIndex( "M7" ) );
     }*/

    /**
     * Article_Pictures::save() - in demo shop mode
     *
     * @return null
     */
    public function testSave_demoShopMode()
    {
        $oConfig = $this->getMock("oxConfig", array("isDemoShop"));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(true));

        oxRegistry::getSession()->deleteVariable("Errors");

        $oArtPic = $this->getProxyClass("Article_Pictures");
        $oArtPic->setConfig($oConfig);
        $oArtPic->save();

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);

        $this->assertTrue($oEx instanceof oxExceptionToDisplay);
    }

    /**
     * Article_Pictures::deletePicture() - in demo shop mode
     *
     * @return null
     */
    public function testDeletePicture_demoShopMode()
    {
        $oConfig = $this->getMock("oxConfig", array("isDemoShop"));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(true));

        oxRegistry::getSession()->deleteVariable("Errors");

        $oArtPic = $this->getProxyClass("Article_Pictures");
        $oArtPic->setConfig($oConfig);
        $oArtPic->deletePicture();

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);

        $this->assertTrue($oEx instanceof oxExceptionToDisplay);
    }

    /**
     * test for bug#0002041: editing inherited product pictures in subshop changes default shop for product
     */
    public function testSubshopStaysSame()
    {
        $oArticle = $this->getMock('oxarticle', array('load', 'save', 'assign'));
        $oArticle->expects($this->once())->method('load')->with($this->equalTo('asdasdasd'))->will($this->returnValue(true));
        $oArticle->expects($this->once())->method('assign')->with($this->equalTo(array('s' => 'test')))->will($this->returnValue(null));
        $oArticle->expects($this->once())->method('save')->will($this->returnValue(null));

        oxTestModules::addModuleObject('oxarticle', $oArticle);

        modConfig::setRequestParameter('oxid', 'asdasdasd');
        modConfig::setRequestParameter('editval', array('s' => 'test'));
        $oArtPic = $this->getProxyClass("Article_Pictures");
        $oArtPic->save();
    }

}
