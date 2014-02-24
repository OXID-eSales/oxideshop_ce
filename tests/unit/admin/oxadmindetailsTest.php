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
 * Test oxLinks module
 */
class modOxLinks_oxAdminView extends oxLinks
{
    /**
     * Force isDerived.
     *
     * @return boolean
     */
    public function isDerived()
    {
        return true;
    }
}

/**
 * Test oxAdminView module
 */
class modOxAdminView_autorized extends oxAdminView
{
    /**
     * Force _authorize.
     *
     * @return boolean
     */
    protected function _authorize()
    {
        return true;
    }
}

/**
 * Test oxAdminView Editor
 */
class modOxAdminView_Editor
{
    /**
     * Skip fetch.
     *
     * @return null
     */
    public function fetch()
    {
    }
}

/**
 * Testing oxAdminDetails class.
 */
class Unit_Admin_oxAdminDetailsTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable( 'oxlinks' );
        $this->cleanUpTable( 'oxorder' );
        $this->cleanUpTable( 'oxcontents' );
        $this->cleanUpTable( 'oxobject2category' );

        oxRemClassModule( 'modOxLinks_oxAdminView' );
        parent::tearDown();
    }

    /**
     * Test generate text editor if WYSIWYG Pro is installed.
     *
     * @return null
     */
    public function testGenerateTextEditorWProIsInstalled()
    {
        $oEditor = $this->getMock( 'modOxAdminView_Editor', array( 'fetch' ) );
        $oEditor->expects( $this->once() )->method( 'fetch' )->with( $this->equalTo( 1 ), $this->equalTo( 2 ) )->will( $this->returnValue( 6 ) );

        $oAdminDetails = $this->getMock( 'oxadmindetails', array( '_getTextEditor', '_getPlainEditor' ) );
        $oAdminDetails->expects( $this->once() )->method( '_getTextEditor' )->with( $this->equalTo( 1 ), $this->equalTo( 2 ), $this->equalTo( 3 ), $this->equalTo( 4 ), $this->equalTo( 5 ) )->will( $this->returnValue( $oEditor ) );
        $oAdminDetails->expects( $this->never() )->method( '_getPlainEditor' );

        $this->assertEquals( 6, $oAdminDetails->UNITgenerateTextEditor( 1, 2, 3, 4, 5 ) );
    }

    /**
     * Test generate text editor if WYSIWYG Pro is not installed.
     *
     * @return null
     */
    public function testGenerateTextEditorNoWPro()
    {
        $oAdminDetails = $this->getMock( 'oxadmindetails', array( '_getTextEditor', '_getPlainEditor' ) );
        $oAdminDetails->expects( $this->once() )->method( '_getTextEditor' )->with( $this->equalTo( 1 ), $this->equalTo( 2 ), $this->equalTo( 3 ), $this->equalTo( 4 ), $this->equalTo( 5 ) )->will( $this->returnValue( null ) );
        $oAdminDetails->expects( $this->once() )->method( '_getPlainEditor' )->with( $this->equalTo( 1 ), $this->equalTo( 2 ), $this->equalTo( 3 ), $this->equalTo( 4 ) )->will( $this->returnValue( 5 ) );

        $this->assertEquals( 5, $oAdminDetails->UNITgenerateTextEditor( 1, 2, 3, 4, 5 ) );
    }

    /**
     * Test get plain editor.
     *
     * @return null
     */
    public function testGetPlainEditor()
    {
        $oObject = new stdClass;
        $sEditorHtml = "<textarea id='editor_sField' style='width:100px; height:100px;'>sEditObjectValue</textarea>";

        $oAdminDetails = $this->getMock( 'oxadmindetails', array( '_getEditValue' ) );
        $oAdminDetails->expects( $this->once() )->method( '_getEditValue' )->with( $this->equalTo( $oObject ), $this->equalTo( 'sField' ) )->will( $this->returnValue( 'sEditObjectValue' ) );
        $this->assertEquals( $sEditorHtml, $oAdminDetails->UNITgetPlainEditor( 100, 100, $oObject, 'sField' ) );
    }

    /**
     * Test get edit value then object is not set.
     *
     * @return null
     */
    public function testGetEditValueObjectNotSet()
    {
        $oAdminDetails = new oxadmindetails();
        $this->assertEquals( '', $oAdminDetails->UNITgetEditValue( null, null ) );
    }

    /**
     * Test get edit value.
     *
     * @return null
     */
    public function testGetEditValue()
    {
        $oObject = new stdClass;
        $oObject->oField1 = new oxField( 'field1value' );

        $oObject->oField2 = new stdClass;
        $oObject->oField2->value = 'field2value';

        $oAdminDetails = new oxadmindetails();
        $this->assertEquals( '', $oAdminDetails->UNITgetEditValue( $oObject, 'notExistingField' ) );
        $this->assertEquals( 'field1value', $oAdminDetails->UNITgetEditValue( $oObject, 'oField1' ) );
        $this->assertEquals( 'field2value', $oAdminDetails->UNITgetEditValue( $oObject, 'oField2' ) );
    }

    /**
     * Test get edit value - when smarty parser is off.
     *
     * @return null
     */
    public function testGetEditValue_parseIsOff()
    {
        $oObject = new stdClass;
        $oObject->oField = new oxField( 'test [{$oViewConf->getCurrentHomeDir()}]' );

        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam( "bl_perfParseLongDescinSmarty", false );
        $sUrl = modConfig::getInstance()->getCurrentShopURL();

        $oAdminDetails = new oxadmindetails();
        $this->assertEquals( "test $sUrl", $oAdminDetails->UNITgetEditValue( $oObject, 'oField' ) );
    }

    /**
     * Test get text editor.
     *
     * @return null
     */
    public function testGetTextEditor()
    {
        $oAdminDetails = new oxadmindetails();

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxtitle = new oxField( "test value" );

        $oEditor = $oAdminDetails->UNITgetTextEditor( 10, 10, $oArticle, 'oxarticles__oxtitle' );

            $this->assertFalse( $oEditor );
    }

    /**
     * Test get text editor - including css files.
     *
     * @return null
     */
    public function testGetTextEditor_cssInclude()
    {
        $oConfig = oxConfig::getInstance();
        $oAdminDetails = new oxadmindetails();
        $oEditor = $oAdminDetails->UNITgetTextEditor( 10, 10, new oxarticle, 'oxarticles__oxtitle', 'oxid_ie6.css' );

            $this->assertFalse( $oEditor );

        $sDefaultCss = "oxid.css";

    }

    /**
     * Test get text editor - including css files.
     *
     * @return null
     */
    public function testGetTextEditor_cssIncludeFormerTemplates()
    {
        $oConfig = modConfig::getInstance();
        $oConfig->setConfigParam( "blFormerTplSupport", true );

        $oAdminDetails = new oxadmindetails();
        $oEditor = $oAdminDetails->UNITgetTextEditor( 10, 10, new oxarticle, 'oxarticles__oxtitle', 'basket.tpl.css' );

            $this->assertFalse( $oEditor );

        $sDefaultCss = "oxid.css";

    }

    /**
     * Provides url data for testGetTextEditor_httpsUrl
     *
     * @return array
     */
    public function urlProvider()
    {
        return array(
            array('https://test_shop_url/', 'https://test_shop_url/core/wysiwigpro/'),
            array('https://test_shop_url', 'https://test_shop_url/core/wysiwigpro/'),
            array('https://test_shop_url/sub/', 'https://test_shop_url/sub/core/wysiwigpro/'),
            array('https://test_shop_url/sub', 'https://test_shop_url/sub/core/wysiwigpro/'),
        );
    }

    /**
     * Test get text editor - uses admin https url if defined.
     *
     * @param string $sShopUrl     shop url to be set
     * @param string $sExpectedUrl expected url retrieved as editor url
     *
     * @dataProvider urlProvider
     *
     * @return null
     */
    public function testGetTextEditor_httpsUrl($sShopUrl, $sExpectedUrl)
    {
        $this->getConfig()->setIsSsl(true);
        $this->getConfig()->setConfigParam('sShopURL', $sShopUrl);
        $this->getConfig()->setConfigParam('sSSLShopURL', $sShopUrl);

        $oAdminDetails = new oxadmindetails();
        $oEditor = $oAdminDetails->UNITgetTextEditor( 10, 10, new oxarticle, 'oxarticles__oxtitle', 'basket.tpl.css' );

            $this->assertFalse( $oEditor );

    }

    /**
     * Test get text editor - uses admin https url if defined.
     *
     * @return null
     */
    public function testGetTextEditor_httpUrl()
    {
        $this->getConfig()->setIsSsl(false);
        $this->getConfig()->setConfigParam('sShopURL', 'http://test_shop_url/');

        $oAdminDetails = new oxadmindetails();
        $oEditor = $oAdminDetails->UNITgetTextEditor( 10, 10, new oxarticle, 'oxarticles__oxtitle', 'basket.tpl.css' );

            $this->assertFalse( $oEditor );

    }

    /**
     *  Test updating object folder parameters
     *
     *  @return null
     */
    public function testChangeFolder()
    {
        $oListItem = oxNew( 'oxContent' );
        $oListItem->setId( '_testId' );
        $oListItem->oxcontents__oxloadid = new oxField( "_testLoadId" );
        $oListItem->save();

        modConfig::setParameter( 'oxid', '_testId' );
        modConfig::setParameter( 'setfolder', 'neu' );
        modConfig::setParameter( 'folderclass', 'oxcontent' );

        $oAdminDetails = $this->getProxyClass( 'oxadmindetails' );
        $oAdminDetails->setNonPublicVar( '_oList', $oListItem );
        $oAdminDetails->changeFolder();

        $sSql = "select oxfolder from oxcontents where oxid = '_testId' ";
        $this->assertEquals( 'neu', oxDb::getDb()->getOne( $sSql ) );
    }

    /**
     *  Test updating object folder parameters - reseting folder
     *
     *  @return null
     */
    public function testChangeFolderResetingFolderName()
    {
        $oListItem = oxNew( 'oxContent' );
        $oListItem->setId( '_testId' );
        $oListItem->oxcontents__oxloadid = new oxField( "_testLoadId" );
        $oListItem->oxcontents__oxfolder = new oxField('neu', oxField::T_RAW);
        $oListItem->save();

        modConfig::setParameter( 'oxid', '_testId' );
        modConfig::setParameter( 'setfolder', 'CMSFOLDER_NONE' );
        modConfig::setParameter( 'folderclass', 'oxcontent' );

        $oAdminDetails = $this->getProxyClass( 'oxadmindetails' );

        $oAdminDetails->setNonPublicVar( '_oList', $oListItem );
        $oAdminDetails->changeFolder();

        $sSql = "select oxfolder from oxcontents where oxid = '_testId' ";
        $this->assertEquals( '', oxDb::getDb()->getOne( $sSql ) );
    }

    /**
     *  Test setup navigation.
     *
     *  @return null
     */
    public function testSetupNavigation()
    {
        $oNavigation = $this->getMock( 'oxnavigationtree', array( 'getBtn', 'getActiveTab' ) );
        $oNavigation->expects( $this->once() )->method( 'getBtn' )->with( $this->equalTo( 'xxx' ) )->will( $this->returnValue( 'bottom_buttons' ) );
        $oNavigation->expects( $this->once() )->method( 'getActiveTab' )->with( $this->equalTo( 'xxx' ), $this->equalTo( 0 ) )->will( $this->returnValue( 'default_edit' ));

        $oAdminDetails = $this->getMock( 'oxadmindetails', array( 'getNavigation' ) );
        $oAdminDetails->expects( $this->once() )->method( 'getNavigation' )->will( $this->returnValue( $oNavigation ) );

        $oAdminDetails->UNITsetupNavigation( 'xxx' );
        $this->assertEquals( 'default_edit', $oAdminDetails->getViewDataElement( 'default_edit' ) );
        $this->assertEquals( 'bottom_buttons', $oAdminDetails->getViewDataElement( 'bottom_buttons' ) );
    }

    /**
     *  Test get category tree testing if empty category will be selected.
     *
     *  @return null
     */
    public function testGetCategoryTreeTestingIfEmptyCategoryWillBeSelected()
    {
        $oAdminDetails = new oxadmindetails();
        $sActCatId = $oAdminDetails->UNITgetCategoryTree( 'xxx', null );
        $oList = $oAdminDetails->getViewDataElement( 'xxx' );
        $oList->rewind();

        $oCat = $oList->current();
        $this->assertEquals( '--', $oCat->oxcategories__oxtitle->value );
        $this->assertEquals( $sActCatId, $oCat->getId() );
    }

    /**
     *  Test get category tree unsetting active category.
     *
     *  @return null
     */
    public function testGetCategoryTreeUnsettingActiveCategory()
    {
        $sCatTable = getViewName( 'oxcategories' );
        $sCat = oxDb::getDb()->getOne( "select oxid from $sCatTable where oxactive = 1" );

        $oAdminDetails = new oxadmindetails();
        $sActCatId = $oAdminDetails->UNITgetCategoryTree( 'xxx', null, $sCat );
        $oList = $oAdminDetails->getViewDataElement( 'xxx' );

        foreach ( $oList as $oCat ) {
            if ( $oCat->getId() == $sCat ) {
                $this->fail( 'failed testGetCategoryTreeUnsettingActiveCategory test' );
            }
        }
    }

    /**
     *  Test get category tree marking active category.
     *
     *  @return null
     */
    public function testGetCategoryTreeMarkingActiveCategory()
    {
        $sCatTable = getViewName( 'oxcategories' );
        $sCat = oxDb::getDb()->getOne( "select oxid from $sCatTable where oxactive = 1" );

        $oAdminDetails = new oxadmindetails();
        $sActCatId = $oAdminDetails->UNITgetCategoryTree( 'xxx', $sCat );
        $oList = $oAdminDetails->getViewDataElement( 'xxx' );

        foreach ( $oList as $oCat ) {
            if ( $oCat->getId() == $sCat && $oCat->selected = 1 ) {
                return;
            }
        }

        $this->fail( 'failed testGetCategoryTreeUnsettingActiveCategory test' );
    }

    /**
     * Test reseting of number of articles in current shop categories.
     *
     * @return null
     */
    public function testResetNrOfCatArticles()
    {
        $oAdminDetails = $this->getMock( 'oxadmindetails', array( 'resetContentCache' ) );
        $oAdminDetails->expects( $this->once() )->method( 'resetContentCache' );

        $oAdminDetails->resetNrOfCatArticles();
    }

    /**
     * Test reseting number of articles in current shop vendors.
     *
     * @return null
     */
    public function testResetNrOfVendorArticles()
    {
        $oAdminDetails = $this->getMock( 'oxadmindetails', array( 'resetContentCache' ) );
        $oAdminDetails->expects( $this->once() )->method( 'resetContentCache' );

        $oAdminDetails->resetNrOfVendorArticles();
    }

    /**
     * Test reseting number of articles in current shop manufacturers.
     *
     * @return null
     */
    public function testResetNrOfManufacturerArticles()
    {
        $oAdminDetails = $this->getMock( 'oxadmindetails', array( 'resetContentCache' ) );
        $oAdminDetails->expects( $this->once() )->method( 'resetContentCache' );

        $oAdminDetails->resetNrOfManufacturerArticles();
    }

    /**
     * Test reseting count of vendor/manufacturer category items
     *
     * @return null
     */
    public function testResetCounts()
    {

        $oAdminDetails = $this->getMock( 'oxadmindetails', array( 'resetCounter' ) );
        $oAdminDetails->expects( $this->at( 0 ) )->method( 'resetCounter' )->with( $this->equalTo( "vendorArticle" ), $this->equalTo( "ID1" ) );
        $oAdminDetails->expects( $this->at( 1 ) )->method( 'resetCounter' )->with( $this->equalTo( "manufacturerArticle" ), $this->equalTo( "ID2" ) );

        $aIds = array( "vendor" => array( "ID1" => "1"), "manufacturer" => array( "ID2" => "2" ) );

        $oAdminDetails->UNITresetCounts( $aIds );
    }
}
