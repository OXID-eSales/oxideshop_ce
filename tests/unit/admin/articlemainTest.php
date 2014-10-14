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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for Article_Main class
 */
class Unit_Admin_ArticleMainTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oArt = oxNew( 'oxarticle' );
        $oArt->delete('_testArtId');

        $oArt = oxNew( 'oxarticle' );
        $oArt->delete('_testArtId2');

        $myDB = oxDb::getDB();
        $myDB->execute( 'delete from oxobject2selectlist' );
        parent::tearDown();
    }

    /**
     * Copying article
     *
     * @return null
     */
    public function testCopyArticleAdditionalTest()
    {
        oxTestModules::addFunction( 'oxarticle', 'load', '{ return true; }');
        oxTestModules::addFunction( 'oxarticle', 'save', '{ return true; }');
        modConfig::getInstance()->setConfigParam( "blDisableDublArtOnCopy", true );

        $aTasks = array( "_copyCategories", "_copyAttributes", "_copySelectlists",
                         "_copyCrossseling", "_copyAccessoires", "_copyStaffelpreis",
                         "_copyArtExtends" );

            $aTasks[] = "_resetCounts";


        $oView = $this->getMock( "Article_Main", $aTasks );
        $oView->expects( $this->once() )->method( '_copyCategories' );
        $oView->expects( $this->once() )->method( '_copyAttributes' );
        $oView->expects( $this->once() )->method( '_copySelectlists' );
        $oView->expects( $this->once() )->method( '_copyCrossseling' );
        $oView->expects( $this->once() )->method( '_copyAccessoires' );
        $oView->expects( $this->once() )->method( '_copyStaffelpreis' );
        $oView->expects( $this->once() )->method( '_copyArtExtends' );

            $oView->expects( $this->once() )->method( '_resetCounts' );


        $oDb = oxDb::getDb();
        $sProdId   = $oDb->getOne( "select oxid from oxarticles where oxparentid !=''" );
        $sParentId = $oDb->getOne( "select oxparentid from oxarticles where oxid ='{$sProdId}'" );

        $oView->copyArticle( $sProdId, "_testArtId", $sParentId );
    }

    /**
     * Copying attributes assignments
     *
     * @return null
     */
    public function testCopyCategories()
    {
        $oDb     = oxDb::getDb();
        $oUtils  = oxUtilsObject::getInstance();
        $iShopId = oxConfig::getInstance()->getShopId();

        $sO2CView = getViewName( 'oxobject2category' );

        // creating few oxprice2article records
        $oDb->execute( "INSERT INTO `{$sO2CView}` (`OXID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`) VALUES ('".$oUtils->generateUId()."', '_testArtId', '_testCatId', '0', '0');" );
        $oDb->execute( "INSERT INTO `{$sO2CView}` (`OXID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`) VALUES ('".$oUtils->generateUId()."', '_testArtId', '_testCatId', '0', '0');" );

        $oView = new Article_Main();
        $oView->UNITcopyCategories( "_testArtId", "_testArtId2" );

        $this->assertEquals( 2, $oDb->getOne( "select count(*) from {$sO2CView} where oxobjectid = '_testArtId2'" ) );
    }

    /**
     * Copying attributes assignments
     *
     * @return null
     */
    public function testCopyAttributes()
    {
        $oDb     = oxDb::getDb();
        $oUtils  = oxUtilsObject::getInstance();
        $iShopId = oxConfig::getInstance()->getShopId();

        // creating few oxprice2article records
        $oDb->execute( "INSERT INTO `oxobject2attribute` (OXID,OXOBJECTID,OXATTRID,OXVALUE,OXPOS,OXVALUE_1,OXVALUE_2,OXVALUE_3) VALUES ('".$oUtils->generateUId()."', '_testArtId', '_testObjId', '0', '0', '0', '0', '0' );" );
        $oDb->execute( "INSERT INTO `oxobject2attribute` (OXID,OXOBJECTID,OXATTRID,OXVALUE,OXPOS,OXVALUE_1,OXVALUE_2,OXVALUE_3) VALUES ('".$oUtils->generateUId()."', '_testArtId', '_testObjId', '0', '0', '0', '0', '0' );" );

        $oView = new Article_Main();
        $oView->UNITcopyAttributes( "_testArtId", "_testArtId2" );

        $this->assertEquals( 2, $oDb->getOne( "select count(*) from oxobject2attribute where oxobjectid = '_testArtId2'" ) );
    }

    /**
     * Copying selectlists assignments
     *
     * @return null
     */
    public function testCopySelectlists()
    {
        $oDb     = oxDb::getDb();
        $oUtils  = oxUtilsObject::getInstance();
        $iShopId = oxConfig::getInstance()->getShopId();

        // creating few oxprice2article records
        $oDb->execute( "INSERT INTO `oxobject2selectlist` (OXID,OXOBJECTID,OXSELNID,OXSORT) VALUES ('".$oUtils->generateUId()."', '_testArtId', '_testObjId', 0);" );
        $oDb->execute( "INSERT INTO `oxobject2selectlist` (OXID,OXOBJECTID,OXSELNID,OXSORT) VALUES ('".$oUtils->generateUId()."', '_testArtId', '_testObjId', 0);" );

        $oView = new Article_Main();
        $oView->UNITcopySelectlists( "_testArtId", "_testArtId2" );

        $this->assertEquals( 2, $oDb->getOne( "select count(*) from oxobject2selectlist where oxobjectid = '_testArtId2'" ) );
    }

    /**
     * Copying files
     *
     * @return null
     */
    public function testCopyFiles()
    {
        $oDb     = oxDb::getDb();
        $oUtils  = oxUtilsObject::getInstance();
        $iShopId = oxConfig::getInstance()->getShopId();

        // creating few files records
        $oDb->execute( "INSERT INTO `oxfiles` (`OXID`, `OXARTID`, `OXFILENAME`) VALUES ('".$oUtils->generateUId()."', '_testArtId', '_testObjId');" );
        $oDb->execute( "INSERT INTO `oxfiles` (`OXID`, `OXARTID`, `OXFILENAME`) VALUES ('".$oUtils->generateUId()."', '_testArtId', '_testObjId');" );

        $oView = new Article_Main();
        $oView->UNITcopyFiles( "_testArtId", "_testArtId2" );

        $this->assertEquals( 2, $oDb->getOne( "SELECT COUNT(*) FROM `oxfiles` WHERE `oxartid` = '_testArtId2'" ) );
    }

    /**
     * Copying crossseling assignments
     *
     * @return null
     */
    public function testCopyCrossseling()
    {
        $oDb     = oxDb::getDb();
        $oUtils  = oxUtilsObject::getInstance();
        $iShopId = oxConfig::getInstance()->getShopId();

        // creating few oxprice2article records
        $oDb->execute( "INSERT INTO `oxobject2article` (OXID,OXOBJECTID,OXARTICLENID,OXSORT) VALUES ('".$oUtils->generateUId()."', '_testObjId', '_testArtId', 0);" );
        $oDb->execute( "INSERT INTO `oxobject2article` (OXID,OXOBJECTID,OXARTICLENID,OXSORT) VALUES ('".$oUtils->generateUId()."', '_testObjId', '_testArtId', 0);" );

        $oView = new Article_Main();
        $oView->UNITcopyCrossseling( "_testArtId", "_testArtId2" );

        $this->assertEquals( 2, $oDb->getOne( "select count(*) from oxobject2article where oxarticlenid = '_testArtId2'" ) );
    }

    /**
     * Copying accessoires assignments
     *
     * @return null
     */
    public function testCopyAccessoires()
    {
        $oDb     = oxDb::getDb();
        $oUtils  = oxUtilsObject::getInstance();
        $iShopId = oxConfig::getInstance()->getShopId();

        // creating few oxprice2article records
        $oDb->execute( "INSERT INTO `oxaccessoire2article` (OXID,OXOBJECTID,OXARTICLENID,OXSORT) VALUES ('".$oUtils->generateUId()."', '_testObjId', '_testArtId', 0);" );
        $oDb->execute( "INSERT INTO `oxaccessoire2article` (OXID,OXOBJECTID,OXARTICLENID,OXSORT) VALUES ('".$oUtils->generateUId()."', '_testObjId', '_testArtId', 0);" );

        $oView = new Article_Main();
        $oView->UNITcopyAccessoires( "_testArtId", "_testArtId2" );

        $this->assertEquals( 2, $oDb->getOne( "select count(*) from oxaccessoire2article where oxarticlenid = '_testArtId2'" ) );

    }

    /**
     * Copying staffelpreis assignments
     *
     * @return null
     */
    public function testCopyStaffelpreis()
    {
        $oDb     = oxDb::getDb();
        $oUtils  = oxUtilsObject::getInstance();
        $iShopId = oxConfig::getInstance()->getShopId();

        // creating few oxprice2article records
        $oDb->execute( "INSERT INTO `oxprice2article` (OXID,OXSHOPID,OXARTID,OXADDABS,OXADDPERC,OXAMOUNT,OXAMOUNTTO) VALUES ('".$oUtils->generateUId()."', '{$iShopId}', '_testArtId', 1, 0, 2, 3);" );
        $oDb->execute( "INSERT INTO `oxprice2article` (OXID,OXSHOPID,OXARTID,OXADDABS,OXADDPERC,OXAMOUNT,OXAMOUNTTO) VALUES ('".$oUtils->generateUId()."', '{$iShopId}', '_testArtId', 0.5, 0, 4, 5);" );

        $oView = new Article_Main();
        $oView->UNITcopyStaffelpreis( "_testArtId", "_testArtId2" );

        $this->assertEquals( 2, $oDb->getOne( "select count(*) from oxprice2article where oxartid = '_testArtId2'" ) );
    }

    /**
     * Copying article extends
     *
     * @return null
     */
    public function testCopyArtExtends()
    {
        oxTestModules::addFunction( 'oxbase', 'save', '{ throw new Exception( "save" ); }');

        try {
            $oView = new Article_Main();
            $oView->UNITcopyArtExtends( "old", "new" );
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in Article_Main::_copyArtExtends()" );
            return;
        }
        $this->fail( "error in Article_Main::_copyArtExtends()" );
    }


    /**
     * Testing Article_Main::saveinnlang()
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        $oView = $this->getMock( "Article_Main", array( "save" ) );
        $oView->expects( $this->once() )->method( 'save' );
        $oView->saveinnlang();
    }

    /**
     * Testing Article_Main::saveinnlang()
     *
     * @return null
     */
    public function testSaveinnlangDefaultId()
    {
        oxTestModules::addFunction( 'oxarticle', 'setLanguage', '{ return true; }');
        oxTestModules::addFunction( 'oxarticle', 'load', '{ return true; }');
        oxTestModules::addFunction( 'oxarticle', 'assign', '{ return true; }');
        oxTestModules::addFunction( 'oxarticle', 'save', '{ throw new Exception( "save" ); }');

        modConfig::setParameter( "oxid", "123" );
        modConfig::setParameter( "oxparentid", "testPArentId" );
        modConfig::setParameter( "editval", array( 'oxarticles__oxvat' => '', 'oxarticles__oxprice' => 999 ) );

        // testing..
        try {

            $aTasks = array();
                $aTasks[] = 'resetCounter';
                $aTasks[] = '_resetCounts';


            $oView = $this->getMock( "Article_Main", $aTasks );

                $oView->expects( $this->once() )->method( 'resetCounter' );
                $oView->expects( $this->once() )->method( '_resetCounts' );


            $oView->saveinnlang();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in Article_Main::saveinnlang()" );
            return;
        }
        $this->fail( "error in Article_Main::saveinnlang()" );
    }

    /**
     * Testing Article_Main::render()
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $sOxid = oxDb::getDb()->getOne( "select oxid from oxarticles where oxparentid !='' " );
        modConfig::setParameter( "oxid", $sOxid );
        //modConfig::setParameter( "voxid", "-1" );
        //modConfig::setParameter( "oxparentid", oxDb::getDb()->getOne( "select oxparentid from oxarticles where oxid ='{$sOxid}' " ) );

        $oView = $this->getProxyClass( "Article_Main" );
        $oView->setNonPublicVar( "_sSavedId", $sOxid );

        $this->assertEquals( "article_main.tpl", $oView->render() );
        $aViewData = $oView->getViewData();
        $this->assertTrue( $aViewData['edit'] instanceof oxarticle );
    }

    /**
     * Testing Article_Main::render()
     *
     * @return null
     */
    public function testRenderLoadingParentArticle()
    {
        $oDb = oxDb::getDb();
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $sOxid = $oDb->getOne( "select oxid from oxarticles where oxparentid !='' " );
        $sParentOxid = $oDb->getOne( "select oxparentid from oxarticles where oxid ='{$sOxid}' " );
        modConfig::setParameter( "voxid", "-1" );
        modConfig::setParameter( "oxparentid", $sParentOxid );

        $oView = new Article_Main();
        $this->assertEquals( "article_main.tpl", $oView->render() );
        $aViewData = $oView->getViewData();
        $this->assertTrue( $aViewData['edit'] instanceof oxarticle );
        $this->assertTrue( $aViewData['parentarticle'] instanceof oxarticle );
        $this->assertEquals( $sParentOxid, $aViewData['oxparentid'] );
        $this->assertEquals( "-1", $aViewData['oxid'] );
    }

    /**
     * Testing Article_Main::addDefaultValues()
     *
     * @return null
     */
    public function testAddDefaultValues()
    {
        $oView = new Article_Main();
        $this->assertEquals( "aaa", $oView->addDefaultValues( "aaa" ) );
    }

    /**
     * Testing Article_Main::_getTitle()
     *
     * @return null
     */
    public function testGetTitle()
    {
        $oO1 = new oxArticle();
        $oO1->oxarticles__oxtitle = $this->getMock( "oxfield", array( "__get" ) );
        $oO1->oxarticles__oxtitle->expects( $this->once() )->method( '__get' )->will( $this->returnValue( "oxtitle" ) );

        $oO2 = new oxArticle();
        $oO2->oxarticles__oxtitle = $this->getMock( "oxfield", array( "__get" ) );
        $oO2->oxarticles__oxtitle->expects( $this->once() )->method( '__get' )->will( $this->returnValue( null ) );
        $oO2->oxarticles__oxvarselect = $this->getMock( "oxfield", array( "__get" ) );
        $oO2->oxarticles__oxvarselect->expects( $this->once() )->method( '__get' )->will( $this->returnValue( "oxvarselect" ) );

        $oView = new Article_Main();
        $this->assertEquals( "oxtitle", $oView->UNITgetTitle( $oO1 ) );
        $this->assertEquals( "oxvarselect", $oView->UNITgetTitle( $oO2 ) );
    }

    /**
     * Testing Article_Main::getCategoryList()
     *
     * @return null
     */
    public function testGetCategoryList()
    {
        $iListSize = oxDb::getDb()->getOne( "select count(*) from oxcategories" );

        $oView = new Article_Main();
        $oList = $oView->getCategoryList();
        $this->assertTrue( $oList instanceof oxCategoryList );
        $this->assertEquals( $iListSize, $oList->count() );
    }

    /**
     * Testing Article_Main::getVendorList()
     *
     * @return null
     */
    public function testGetVendorList()
    {
        $iListSize = oxDb::getDb()->getOne( "select count(*) from oxvendor" );

        $oView = new Article_Main();
        $oList = $oView->getVendorList();
        $this->assertTrue( $oList instanceof oxvendorlist );
        $this->assertEquals( $iListSize, $oList->count() );
    }

    /**
     * Testing Article_Main::getManufacturerList()
     *
     * @return null
     */
    public function testGetManufacturerList()
    {
        $iListSize = oxDb::getDb()->getOne( "select count(*) from oxmanufacturers" );

        $oView = new Article_Main();
        $oList = $oView->getManufacturerList();
        $this->assertTrue( $oList instanceof oxmanufacturerlist );
        $this->assertEquals( $iListSize, $oList->count() );
    }

    /**
     * Testing blWarnOnSameArtNums option ( FS#2489 )
     *
     * @return null
     */
    public function testCopyArticle()
    {
        modConfig::getInstance()->setConfigParam( 'blWarnOnSameArtNums', true );
        modConfig::setParameter( 'fnc', 'copyArticle' );
        $oArtView = $this->getProxyClass("article_main");
        $oArtView->copyArticle( '2000', '_testArtId' );
        $aViewData = $oArtView->getNonPublicVar( '_aViewData' );
        $this->assertEquals(1, $aViewData["errorsavingatricle"]);
    }

    /**
     * Testing if before saving spaces are trimed from article title
     *
     * @return null
     */
    public function testSaveTrimsArticleTitle()
    {
        $oConfig = oxConfig::getInstance();
        modConfig::setParameter( 'oxid', '_testArtId' );
        $aParams['oxid'] = '_testArtId';
        $aParams['oxarticles__oxtitle'] = ' _testArticleTitle   ';

        $oArticle = oxNew( 'oxArticle' );
        $oArticle->setId( $aParams['oxid'] );
        $oArticle->oxarticles__oxtitle = new oxField( $aParams['oxarticles__oxtitle'] );
        $oArticle->save();

        modConfig::setParameter( "editval", $aParams );

        $oArtView = $this->getProxyClass( "article_main" );
        $oArtView->save();

        $oArticle = oxNew( 'oxArticle' );
        $oArticle->load( '_testArtId' );
        $this->assertEquals( '_testArticleTitle', $oArticle->oxarticles__oxtitle->value );
    }

    /**
     * Testing if before saving spaces are trimed from article title
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction( 'oxarticle', 'save', '{ return true; }');
        oxTestModules::addFunction( 'oxarticle', 'assignRecord', '{ return true; }');
        oxTestModules::addFunction( 'oxarticletaglist', 'save', '{ throw new Exception( "saveTags" ); }');

        modConfig::setParameter( "oxid", -1 );
        modConfig::setParameter( "oxparentid", "-1" );
        modConfig::setParameter( "editval", array( 'tags' => 'test tags', "oxarticles__oxparentid" => "-1", "oxarticles__oxartnum" => "123", "oxarticles__oxprice" => "123", "oxarticles__oxactive" => 1 ) );

        $oArtView = new article_main();

        try {
            $oArtView->save();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "saveTags", $oExcp->getMessage(), "error in Article_Main::save()" );
            return;
        }
        $this->fail( "error in Article_Main::save()" );
    }

    /**
     * Testing if before saving spaces are trimed from article title
     *
     * @return null
     */
    public function testSaveNoParent()
    {
        oxTestModules::addFunction( 'oxarticle', 'save', '{ return true; }');
        oxTestModules::addFunction( 'oxarticle', 'assignRecord', '{ return true; }');
        oxTestModules::addFunction( 'oxarticletaglist', 'save', '{ throw new Exception( "saveTags" ); }');

        modConfig::setParameter( "oxid", -1 );
        modConfig::setParameter( "oxparentid", "132" );
        modConfig::setParameter( "editval", array( 'tags' => 'test tags', "oxarticles__oxparentid" => "-1", "oxarticles__oxartnum" => "123", "oxarticles__oxprice" => "123", "oxarticles__oxactive" => 1 ) );

        $oArtView = new article_main();

        try {
            $oArtView->save();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "saveTags", $oExcp->getMessage(), "error in Article_Main::save()" );
            return;
        }
        $this->fail( "error in Article_Main::save()" );
    }

    /**
     * Testing article_main::_formJumpList();
     *
     * @return null
     */
    public function testFormJumpListParent()
    {
        $oVar1 = new oxArticle();
        $oVar1->oxarticles__oxid = new oxField( "testId1" );

        $oVar2 = new oxArticle();
        $oVar2->oxarticles__oxid = new oxField( "testId2" );

        $oParentVariants = new oxList();
        $oParentVariants->offsetSet( "var1", $oVar1 );
        $oParentVariants->offsetSet( "var2", $oVar2 );

        $oParentArticle = $this->getMock( "article_main", array( "getAdminVariants" ) );
        $oParentArticle->expects( $this->once() )->method( 'getAdminVariants' )->will( $this->returnValue( $oParentVariants ) );
        $oParentArticle->oxarticles__oxid = new oxField( "testParentId" );

        $oVariants = new oxList();
        $oVariants->offsetSet( "var1", $oVar1 );
        $oVariants->offsetSet( "var2", $oVar2 );

        $oArticle = $this->getMock( "oxArticle", array( "getAdminVariants" ) );
        $oArticle->expects( $this->once() )->method( 'getAdminVariants' )->will( $this->returnValue( $oVariants ) );
        $oArticle->oxarticles__oxid = new oxField( "testId2" );

        $aData = array( array( "testParentId", "testTitle" ),
                        array( "testId1", " - testTitle" ),
                        array( "testId2", " - testTitle" ),
                        array( "testId1", " -- testTitle" ),
                        array( "testId2", " -- testTitle" ));

        $oView = $this->getMock( "article_main", array( "_getTitle" ) );
        $oView->expects( $this->atLeastOnce() )->method( '_getTitle' )->will( $this->returnValue( "testTitle" ) );
        $oView->UNITformJumpList( $oArticle, $oParentArticle );
        $this->assertEquals( $aData, $oView->getViewDataElement( "thisvariantlist" ) );
    }

    /**
     * Testing article_main::_formJumpList();
     *
     * @return null
     */
    public function testFormJumpList()
    {
        $oVar1 = new oxArticle();
        $oVar1->oxarticles__oxid = new oxField( "testId1" );

        $oVar2 = new oxArticle();
        $oVar2->oxarticles__oxid = new oxField( "testId2" );

        $oVariants = new oxList();
        $oVariants->offsetSet( "var1", $oVar1 );
        $oVariants->offsetSet( "var2", $oVar2 );

        $oArticle = $this->getMock( "oxArticle", array( "getAdminVariants" ) );
        $oArticle->expects( $this->once() )->method( 'getAdminVariants' )->will( $this->returnValue( $oVariants ) );
        $oArticle->oxarticles__oxid = new oxField( "testId2" );

        $aData = array( array( "testId2", "testTitle" ),
                        array( "testId1", " - testTitle" ),
                        array( "testId2", " - testTitle" ));

        $oView = $this->getMock( "article_main", array( "_getTitle" ) );
        $oView->expects( $this->atLeastOnce() )->method( '_getTitle' )->will( $this->returnValue( "testTitle" ) );
        $oView->UNITformJumpList( $oArticle, null );
        $this->assertEquals( $aData, $oView->getViewDataElement( "thisvariantlist" ) );
    }

    /**
     * Testing if rating set to 0 when article will copied;
     *
     * @return null
     */
    public function testCopyArticleSkipsRating()
    {
        $oArt = new oxarticle();
        $oArt->setId("_testArtId");
        $oArt->oxarticles__oxrating    = new oxField( 10 );
        $oArt->oxarticles__oxratingcnt = new oxField( 110 );
        $oArt->save();

        $oV = new Article_Main();
        $oV->copyArticle('_testArtId', '_testArtId2');

        $oArt = new oxarticle();
        $this->assertTrue($oArt->load('_testArtId2'));
        $this->assertEquals(0, $oArt->oxarticles__oxrating->value);
        $this->assertEquals(0, $oArt->oxarticles__oxratingcnt->value);
    }
}
