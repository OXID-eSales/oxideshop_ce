<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop class
 */
class ModuleTest extends \OxidTestCase
{

    /**
     * Theme::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Module');
        $this->assertEquals('module.tpl', $oView->render());
    }
}
