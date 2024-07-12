<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Country class
 */
class CountryTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Country::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Country');
        $this->assertSame('country', $oView->render());
    }
}
