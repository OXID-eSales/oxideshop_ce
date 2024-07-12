<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop class
 */
class ThemeTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Theme::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Theme');
        $this->assertSame('theme', $oView->render());
    }
}
