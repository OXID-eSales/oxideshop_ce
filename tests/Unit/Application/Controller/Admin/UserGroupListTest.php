<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for UserGroup_List class
 */
class UserGroupListTest extends \OxidTestCase
{

    /**
     * UserGroup_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('UserGroup_List');
        $this->assertEquals('usergroup_list.tpl', $oView->render());
    }
}
