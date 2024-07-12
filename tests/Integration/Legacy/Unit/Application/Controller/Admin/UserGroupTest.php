<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for UserGroup class
 */
class UserGroupTest extends \PHPUnit\Framework\TestCase
{

    /**
     * UserGroup::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('UserGroup');
        $this->assertSame('usergroup', $oView->render());
    }
}
