<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Language class
 */
class LanguageTest extends \OxidTestCase
{

    /**
     * Language::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Language');
        $this->assertEquals('language.tpl', $oView->render());
    }
}
