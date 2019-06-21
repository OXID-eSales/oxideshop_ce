<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

use OxidEsales\EshopCommunity\Application\Model\ArticleList;

/**
 * Tests for oxwAction class
 */
class ActionsTest extends \OxidTestCase
{

    /**
     * Fixture tearDown
     */
    protected function tearDown()
    {
        $query = "UPDATE oxactions2article set OXSORT = 0 WHERE OXACTIONID = 'oxtop5' AND OXSORT = 666";
        \oxDb::getDb()->execute($query);

        parent::tearDown();
    }

    /**
     * Testing oxwAction::render()
     */
    public function testRender()
    {
        $action = oxNew('oxwActions');
        $this->assertSame('widget/product/action.tpl', $action->render());
    }

    /**
     * Testing oxwAction::getAction()
     */
    public function testGetAction()
    {
        $query = "UPDATE oxactions2article set OXSORT = 666 WHERE OXACTIONID = 'oxtop5' AND OXSORT = 0 AND OXARTID not in (2028, 2080)";
        \oxDb::getDb()->execute($query);

        $topProductCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 6 : 4;
        $topProductId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2028' : '2080';

        $this->getConfig()->setConfigParam('bl_perfLoadAktion', 1);

        $action = oxNew('oxwActions');
        $action->setViewParameters(array('action' => 'oxtop5'));
        $aList = $action->getAction();
        $this->assertTrue($aList instanceof ArticleList);
        $this->assertSame($topProductCount, $aList->count());
        $this->assertSame($topProductId, $aList->current()->getId());
    }

    /**
     * Testing oxwAction::getActionName()
     */
    public function testGetActionName()
    {
        $action = oxNew('oxwActions');
        $action->setViewParameters(array('action' => 'oxbargain'));
        $this->assertSame('Angebot der Woche', $action->getActionName());
    }

    /**
     * Testing oxwAction::getListType()
     */
    public function testGetListType()
    {
        $action = oxNew('oxwActions');
        $action->setViewParameters(array('listtype' => 'grid'));
        $this->assertSame('grid', $action->getListType());
    }
}
