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
namespace Unit\Application\Controller\Admin;

use \oxLinks;
use \oxAdminView;
use \stdClass;
use \oxField;
use \oxDb;

/**
 * Testing oxAdminDetails class.
 */
class AdminDetailsTest extends \OxidTestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxlinks');
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxcontents');
        $this->cleanUpTable('oxobject2category');

        parent::tearDown();
    }

    /**
     * Test generate text editor if WYSIWYG Pro is installed.
     */
    public function testGenerateTextEditorWProIsInstalled()
    {
        $oEditor = $this->getMock('modOxAdminView_Editor', array('fetch'));
        $oEditor->expects($this->once())->method('fetch')->with($this->equalTo(1), $this->equalTo(2))->will($this->returnValue(6));

        $oAdminDetails = $this->getMock('oxadmindetails', array('_getTextEditor', '_getPlainEditor'));
        $oAdminDetails->expects($this->once())->method('_getTextEditor')->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4), $this->equalTo(5))->will($this->returnValue($oEditor));
        $oAdminDetails->expects($this->never())->method('_getPlainEditor');

        $this->assertEquals(6, $oAdminDetails->UNITgenerateTextEditor(1, 2, 3, 4, 5));
    }

    /**
     * Test generate text editor if WYSIWYG Pro is not installed.
     */
    public function testGenerateTextEditorNoWPro()
    {
        $oAdminDetails = $this->getMock('oxadmindetails', array('_getTextEditor', '_getPlainEditor'));
        $oAdminDetails->expects($this->once())->method('_getTextEditor')->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4), $this->equalTo(5))->will($this->returnValue(null));
        $oAdminDetails->expects($this->once())->method('_getPlainEditor')->with($this->equalTo(1), $this->equalTo(2), $this->equalTo(3), $this->equalTo(4))->will($this->returnValue(5));

        $this->assertEquals(5, $oAdminDetails->UNITgenerateTextEditor(1, 2, 3, 4, 5));
    }

    /**
     * Test get plain editor.
     */
    public function testGetPlainEditor()
    {
        $oObject = new stdClass;
        $sEditorHtml = "<textarea id='editor_sField' style='width:100px; height:100px;'>sEditObjectValue</textarea>";

        $oAdminDetails = $this->getMock('oxadmindetails', array('_getEditValue'));
        $oAdminDetails->expects($this->once())->method('_getEditValue')->with($this->equalTo($oObject), $this->equalTo('sField'))->will($this->returnValue('sEditObjectValue'));
        $this->assertEquals($sEditorHtml, $oAdminDetails->UNITgetPlainEditor(100, 100, $oObject, 'sField'));
    }

    /**
     * Test get edit value then object is not set.
     */
    public function testGetEditValueObjectNotSet()
    {
        $oAdminDetails = oxNew('oxadmindetails');
        $this->assertEquals('', $oAdminDetails->UNITgetEditValue(null, null));
    }

    /**
     * Test get edit value.
     */
    public function testGetEditValue()
    {
        $oObject = new stdClass;
        $oObject->oField1 = new oxField('field1value');

        $oObject->oField2 = new stdClass;
        $oObject->oField2->value = 'field2value';

        $oAdminDetails = oxNew('oxadmindetails');
        $this->assertEquals('', $oAdminDetails->UNITgetEditValue($oObject, 'notExistingField'));
        $this->assertEquals('field1value', $oAdminDetails->UNITgetEditValue($oObject, 'oField1'));
        $this->assertEquals('field2value', $oAdminDetails->UNITgetEditValue($oObject, 'oField2'));
    }

    /**
     * Test get edit value - when smarty parser is off.
     */
    public function testGetEditValue_parseIsOff()
    {
        $oObject = new stdClass;
        $oObject->oField = new oxField('test [{$oViewConf->getCurrentHomeDir()}]');

        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("bl_perfParseLongDescinSmarty", false);
        $sUrl = $this->getConfig()->getCurrentShopURL();

        $oAdminDetails = oxNew('oxadmindetails');
        $this->assertEquals("test $sUrl", $oAdminDetails->UNITgetEditValue($oObject, 'oField'));
    }

    /**
     *  Test updating object folder parameters
     */
    public function testChangeFolder()
    {
        $oListItem = oxNew('oxContent');
        $oListItem->setId('_testId');
        $oListItem->oxcontents__oxloadid = new oxField("_testLoadId");
        $oListItem->save();

        $this->setRequestParameter('oxid', '_testId');
        $this->setRequestParameter('setfolder', 'neu');
        $this->setRequestParameter('folderclass', 'oxcontent');

        $oAdminDetails = $this->getProxyClass('oxadmindetails');
        $oAdminDetails->setNonPublicVar('_oList', $oListItem);
        $oAdminDetails->changeFolder();

        $sSql = "select oxfolder from oxcontents where oxid = '_testId' ";
        $this->assertEquals('neu', oxDb::getDb()->getOne($sSql));
    }

    /**
     *  Test updating object folder parameters - reseting folder
     */
    public function testChangeFolderResetingFolderName()
    {
        $oListItem = oxNew('oxContent');
        $oListItem->setId('_testId');
        $oListItem->oxcontents__oxloadid = new oxField("_testLoadId");
        $oListItem->oxcontents__oxfolder = new oxField('neu', oxField::T_RAW);
        $oListItem->save();

        $this->setRequestParameter('oxid', '_testId');
        $this->setRequestParameter('setfolder', 'CMSFOLDER_NONE');
        $this->setRequestParameter('folderclass', 'oxcontent');

        $oAdminDetails = $this->getProxyClass('oxadmindetails');

        $oAdminDetails->setNonPublicVar('_oList', $oListItem);
        $oAdminDetails->changeFolder();

        $sSql = "select oxfolder from oxcontents where oxid = '_testId' ";
        $this->assertEquals('', oxDb::getDb()->getOne($sSql));
    }

    /**
     *  Test setup navigation.
     */
    public function testSetupNavigation()
    {
        $oNavigation = $this->getMock('oxnavigationtree', array('getBtn', 'getActiveTab'));
        $oNavigation->expects($this->once())->method('getBtn')->with($this->equalTo('xxx'))->will($this->returnValue('bottom_buttons'));
        $oNavigation->expects($this->once())->method('getActiveTab')->with($this->equalTo('xxx'), $this->equalTo(0))->will($this->returnValue('default_edit'));

        $oAdminDetails = $this->getMock('oxadmindetails', array('getNavigation'));
        $oAdminDetails->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));

        $oAdminDetails->UNITsetupNavigation('xxx');
        $this->assertEquals('default_edit', $oAdminDetails->getViewDataElement('default_edit'));
        $this->assertEquals('bottom_buttons', $oAdminDetails->getViewDataElement('bottom_buttons'));
    }

    /**
     *  Test get category tree testing if empty category will be selected.
     */
    public function testGetCategoryTreeTestingIfEmptyCategoryWillBeSelected()
    {
        $oAdminDetails = oxNew('oxadmindetails');
        $sActCatId = $oAdminDetails->UNITgetCategoryTree('xxx', null);
        $oList = $oAdminDetails->getViewDataElement('xxx');
        $oList->rewind();

        $oCat = $oList->current();
        $this->assertEquals('--', $oCat->oxcategories__oxtitle->value);
        $this->assertEquals($sActCatId, $oCat->getId());
    }

    /**
     *  Test get category tree unsetting active category.
     */
    public function testGetCategoryTreeUnsettingActiveCategory()
    {
        $sCatTable = getViewName('oxcategories');
        $sCat = oxDb::getDb()->getOne("select oxid from $sCatTable where oxactive = 1");

        $oAdminDetails = oxNew('oxadmindetails');
        $sActCatId = $oAdminDetails->UNITgetCategoryTree('xxx', null, $sCat);
        $oList = $oAdminDetails->getViewDataElement('xxx');

        foreach ($oList as $oCat) {
            if ($oCat->getId() == $sCat) {
                $this->fail('failed testGetCategoryTreeUnsettingActiveCategory test');
            }
        }
    }

    /**
     *  Test get category tree marking active category.
     */
    public function testGetCategoryTreeMarkingActiveCategory()
    {
        $sCatTable = getViewName('oxcategories');
        $sCat = oxDb::getDb()->getOne("select oxid from $sCatTable where oxactive = 1");

        $oAdminDetails = oxNew('oxadmindetails');
        $sActCatId = $oAdminDetails->UNITgetCategoryTree('xxx', $sCat);
        $oList = $oAdminDetails->getViewDataElement('xxx');

        foreach ($oList as $oCat) {
            if ($oCat->getId() == $sCat && $oCat->selected = 1) {
                return;
            }
        }

        $this->fail('failed testGetCategoryTreeUnsettingActiveCategory test');
    }

    /**
     * Test reseting of number of articles in current shop categories.
     */
    public function testResetNrOfCatArticles()
    {
        $oAdminDetails = $this->getMock('oxadmindetails', array('resetContentCache'));
        $oAdminDetails->expects($this->once())->method('resetContentCache');

        $oAdminDetails->resetNrOfCatArticles();
    }

    /**
     * Test reseting number of articles in current shop vendors.
     */
    public function testResetNrOfVendorArticles()
    {
        $oAdminDetails = $this->getMock('oxadmindetails', array('resetContentCache'));
        $oAdminDetails->expects($this->once())->method('resetContentCache');

        $oAdminDetails->resetNrOfVendorArticles();
    }

    /**
     * Test reseting number of articles in current shop manufacturers.
     */
    public function testResetNrOfManufacturerArticles()
    {
        $oAdminDetails = $this->getMock('oxadmindetails', array('resetContentCache'));
        $oAdminDetails->expects($this->once())->method('resetContentCache');

        $oAdminDetails->resetNrOfManufacturerArticles();
    }

    /**
     * Test reset count of vendor/manufacturer category items
     */
    public function testResetCounts()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oAdminDetails = $this->getMock('oxadmindetails', array('resetCounter'));
        $oAdminDetails->expects($this->at(0))->method('resetCounter')->with($this->equalTo("vendorArticle"), $this->equalTo("ID1"));
        $oAdminDetails->expects($this->at(1))->method('resetCounter')->with($this->equalTo("manufacturerArticle"), $this->equalTo("ID2"));

        $aIds = array("vendor" => array("ID1" => "1"), "manufacturer" => array("ID2" => "2"));

        $oAdminDetails->UNITresetCounts($aIds);
    }
}
