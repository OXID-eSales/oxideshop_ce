<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Country class
 */
class CountryTest extends \OxidTestCase
{

    /**
     * Country::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Country');
        $this->assertEquals('country.tpl', $oView->render());
    }
}
