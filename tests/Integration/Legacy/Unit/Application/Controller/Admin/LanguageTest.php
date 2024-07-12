<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Language class
 */
class LanguageTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Language::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Language');
        $this->assertSame('language', $oView->render());
    }
}
