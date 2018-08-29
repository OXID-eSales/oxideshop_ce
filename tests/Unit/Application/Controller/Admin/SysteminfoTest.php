<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxField;
use \oxTestModules;

/**
 * Tests for sysreq class
 */
class SysteminfoTest extends \OxidTestCase
{

    /**
     * sysreq::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{ return "Access denied !"; }');
        oxTestModules::addFunction('oxuser', 'loadAdminUser', '{ $this->oxuser__oxrights = new oxField( "justadmin" ); }');

        // testing..
        $oView = oxNew('systeminfo');
        $this->assertEquals("Access denied !", $oView->render());
    }
}
