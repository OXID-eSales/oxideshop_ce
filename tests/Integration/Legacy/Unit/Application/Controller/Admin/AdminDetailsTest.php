<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use oxDb;
use oxField;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\Facts\Facts;
use stdClass;

class AdminDetailsTest extends \OxidTestCase
{
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxlinks');
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxcontents');
        $this->cleanUpTable('oxobject2category');

        parent::tearDown();
    }

    /**
     * Test get edit value then object is not set.
     */
    public function testGetEditValueObjectNotSet()
    {
        $oAdminDetails = oxNew('oxadmindetails');
        $this->assertEquals('', $oAdminDetails->getEditValue(null, null));
    }

    /**
     * Test get edit value.
     */
    public function testGetEditValue()
    {
        $oObject = new stdClass();
        $oObject->oField1 = new oxField('field1value');

        $oObject->oField2 = new stdClass();
        $oObject->oField2->value = 'field2value';

        $oAdminDetails = oxNew('oxadmindetails');
        $this->assertEquals('', $oAdminDetails->getEditValue($oObject, 'notExistingField'));
        $this->assertEquals('field1value', $oAdminDetails->getEditValue($oObject, 'oField1'));
        $this->assertEquals('field2value', $oAdminDetails->getEditValue($oObject, 'oField2'));
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
        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ['getBtn', 'getActiveTab']);
        $oNavigation->expects($this->once())->method('getBtn')->with($this->equalTo('xxx'))->will($this->returnValue('bottom_buttons'));
        $oNavigation->expects($this->once())->method('getActiveTab')->with($this->equalTo('xxx'), $this->equalTo(0))->will($this->returnValue('default_edit'));

        $oAdminDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController::class, ['getNavigation']);
        $oAdminDetails->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));

        $oAdminDetails->setupNavigation('xxx');
        $this->assertEquals('default_edit', $oAdminDetails->getViewDataElement('default_edit'));
        $this->assertEquals('bottom_buttons', $oAdminDetails->getViewDataElement('bottom_buttons'));
    }

    /**
     *  Test get category tree testing if empty category will be selected.
     */
    public function testGetCategoryTreeTestingIfEmptyCategoryWillBeSelected()
    {
        $oAdminDetails = oxNew('oxadmindetails');
        $sActCatId = $oAdminDetails->getCategoryTree('xxx', null);
        $oList = $oAdminDetails->getViewDataElement('xxx');
        $oList->rewind();

        $oCat = $oList->current();
        $this->assertEquals('--', $oCat->oxcategories__oxtitle->value);
        $this->assertEquals($sActCatId, $oCat->getId());
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetCategoryTreeUnsettingActiveCategory(): void
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sCatTable = $tableViewNameGenerator->getViewName('oxcategories');
        $sCat = oxDb::getDb()->getOne(sprintf('select oxid from %s where oxactive = 1', $sCatTable));

        $oAdminDetails = oxNew('oxadmindetails');
        $oList = $oAdminDetails->getViewDataElement('xxx');

        foreach ($oList as $oCat) {
            if ($oCat->getId() == $sCat) {
                $this->fail('failed testGetCategoryTreeUnsettingActiveCategory test');
            }
        }
    }

    #[\PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testGetCategoryTreeMarkingActiveCategory(): void
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sCatTable = $tableViewNameGenerator->getViewName('oxcategories');
        $sCat = oxDb::getDb()->getOne(sprintf('select oxid from %s where oxactive = 1', $sCatTable));

        $oAdminDetails = oxNew('oxadmindetails');
        $oAdminDetails->getCategoryTree('xxx', $sCat);

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
        $oAdminDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController::class, ['resetContentCache']);
        $oAdminDetails->expects($this->once())->method('resetContentCache');

        $oAdminDetails->resetNrOfCatArticles();
    }

    /**
     * Test reseting number of articles in current shop vendors.
     */
    public function testResetNrOfVendorArticles()
    {
        $oAdminDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController::class, ['resetContentCache']);
        $oAdminDetails->expects($this->once())->method('resetContentCache');

        $oAdminDetails->resetNrOfVendorArticles();
    }

    /**
     * Test reseting number of articles in current shop manufacturers.
     */
    public function testResetNrOfManufacturerArticles()
    {
        $oAdminDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController::class, ['resetContentCache']);
        $oAdminDetails->expects($this->once())->method('resetContentCache');

        $oAdminDetails->resetNrOfManufacturerArticles();
    }

    /**
     * Test reset count of vendor/manufacturer category items
     */
    public function testResetCounts()
    {
        if ((new Facts())->getEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oAdminDetails = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController::class, ['resetCounter']);
        $oAdminDetails
            ->method('resetCounter')
            ->withConsecutive(['vendorArticle'], ['manufacturerArticle'])
            ->willReturnOnConsecutiveCalls(
                'ID1',
                'ID2'
            );

        $aIds = ["vendor" => ["ID1" => "1"], "manufacturer" => ["ID2" => "2"]];

        $oAdminDetails->resetCounts($aIds);
    }
}
