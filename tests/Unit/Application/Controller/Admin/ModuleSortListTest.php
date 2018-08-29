<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop_Config class
 */
class ModuleSortListTest extends \OxidTestCase
{

    /**
     * Module_SortList::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Module_SortList');
        $this->assertEquals('module_sortlist.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['aExtClasses']));
        $this->assertTrue(isset($aViewData['aDisabledModules']));
    }

    /**
     * Module_SortList::save()
     *
     * @return null
     */
    public function testSave()
    {
        $this->setAdminMode(true);

        $json = json_encode(array("oxarticle" => array("dir1/module1", "dir2/module2")));
        $this->setRequestParameter("aModules", $json);

        $aModules = array("oxarticle" => "dir1/module1&dir2/module2");

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('saveShopConfVar'));
        $oConfig->expects($this->once())->method('saveShopConfVar')->with($this->equalTo("aarr"), $this->equalTo("aModules"), $this->equalTo($aModules));

        $oView = oxNew('Module_SortList');
        $oView->setConfig($oConfig);

        $oView->save();
    }

    /**
     * Module_SortList::remove()
     *
     * @return null
     */
    public function testRemove()
    {
        $this->setRequestParameter("noButton", true);
        $oView = oxNew('Module_SortList');
        $oView->remove();
        $this->assertTrue($this->getSession()->getVariable("blSkipDeletedExtChecking"));
    }
}
