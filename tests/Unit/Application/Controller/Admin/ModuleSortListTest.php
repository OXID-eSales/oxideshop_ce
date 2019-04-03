<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ModuleSortList;
use OxidEsales\Eshop\Application\Model\Article;

/**
 * Tests for Shop_Config class
 */
class ModuleSortListTest extends \OxidTestCase
{
    public function testRender()
    {
        $oView = oxNew(ModuleSortList::class);
        $this->assertEquals('module_sortlist.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['aExtClasses']));
        $this->assertTrue(isset($aViewData['aDisabledModules']));
    }

    public function testSave()
    {
        $this->setAdminMode(true);

        $chain = [
            Article::class => [
                'dir1/module1',
                'dir2/module2',
            ]
        ];

        $this->setRequestParameter('aModules', json_encode($chain));

        $moduleSortList = oxNew(ModuleSortList::class);
        $moduleSortList->save();

        $moduleSortList->render();

        $viewData = $moduleSortList->getViewData();
        $this->assertSame(
            [
                'OxidEsales---Eshop---Application---Model---Article' => [
                    'dir1/module1',
                    'dir2/module2',
                ]
            ],
            $viewData['aExtClasses']
        );
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
