<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Actions class
 */
class ActionsTest extends \OxidTestCase
{

    /**
     * Actions::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Actions');
        $this->assertEquals('actions.tpl', $oView->render());
    }
}
