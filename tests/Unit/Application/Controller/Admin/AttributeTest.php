<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Attribute class
 */
class AttributeTest extends \OxidTestCase
{

    /**
     * Attribute::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Attribute');
        $this->assertEquals('attribute.tpl', $oView->render());
    }
}
