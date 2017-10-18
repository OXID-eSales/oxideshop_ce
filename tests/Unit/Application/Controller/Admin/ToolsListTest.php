<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Tools_List class
 */
class ToolsListTest extends \OxidTestCase
{

    /**
     * Tools_List::Performsql() test case
     *
     * @return null
     */
    public function testPerformsql()
    {
        // testing..
        $this->getSession()->setVariable('auth', "oxdefaultadmin");
        $this->setRequestParameter("updatesql", 'select * from oxvoucher');

        $oView = oxNew('Tools_List');
        $oView->performsql();
        $this->assertTrue(isset($oView->aSQLs));
    }

    /**
     * Tools_List::ProcessFiles() test case
     *
     * @return null
     */
    public function testProcessFiles()
    {
        // testing..
        $_FILES['myfile']['name'] = array("test.txt");

        $oView = oxNew('Tools_List');
        $this->assertNull($oView->UNITprocessFiles());
    }

    /**
     * Tools_List::PrepareSQL() test case
     *
     * @return null
     */
    public function testPrepareSQL()
    {
        // defining parameters
        $sSQL = 'select * from oxvoucher';
        $iSQLlen = '';

        // testing..
        $oView = oxNew('Tools_List');
        $this->assertTrue($oView->UNITprepareSQL($sSQL, $iSQLlen));
        $this->assertTrue(isset($oView->aSQLs));
    }

    /**
     * Tools_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Tools_List');
        $this->assertEquals('tools_list.tpl', $oView->render());
    }

    /**
     * Tools_List::updateViews() test case
     *
     * @return null
     */
    public function testUpdateViews()
    {
        $this->getSession()->setVariable('malladmin', true);

        $oView = oxNew('Tools_List');
        $oView->updateViews();

        // assert that updating was successful
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData['blViewSuccess']);
    }
}
