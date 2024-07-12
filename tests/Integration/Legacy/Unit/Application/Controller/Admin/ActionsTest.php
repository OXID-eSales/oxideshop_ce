<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Actions class
 */
class ActionsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Actions::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Actions');
        $this->assertSame('actions', $oView->render());
    }
}
