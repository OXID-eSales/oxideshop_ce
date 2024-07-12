<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwManufacturerList class
 */
class ManufacturerListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing oxwManufacturerList::render()
     */
    public function testRender()
    {
        $oManufacturerList = oxNew('oxwManufacturerList');
        $this->assertEquals('widget/footer/manufacturerlist', $oManufacturerList->render());
    }
}
