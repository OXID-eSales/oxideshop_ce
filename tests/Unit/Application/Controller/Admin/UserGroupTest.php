<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for UserGroup class
 */
class UserGroupTest extends \OxidTestCase
{

    /**
     * UserGroup::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('UserGroup');
        $this->assertEquals('usergroup.tpl', $oView->render());
    }
}
