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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Application\Component\Widget;

use oxArticleList;


/**
 * Tests for oxwAction class
 */
class ActionsTest extends \OxidTestCase
{
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
        $topProductCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 6 : 4;
        $topProductId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2028' : '2080';

        $this->getConfig()->setConfigParam('bl_perfLoadAktion', 1);

        $action = oxNew('oxwActions');
        $action->setViewParameters(array('action' => 'oxtop5'));
        $aList = $action->getAction();
        $this->assertTrue($aList instanceof oxArticleList);
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
