<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for UserGroup_List class
 */
class UserGroupListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * UserGroup_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('UserGroup_List');
        $this->assertSame('usergroup_list', $oView->render());
    }
}
