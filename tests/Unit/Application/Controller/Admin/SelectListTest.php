<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for SelectList class
 */
class SelectListTest extends \OxidTestCase
{

    /**
     * SelectList::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('SelectList');
        $this->assertEquals('selectlist.tpl', $oView->render());
    }
}
