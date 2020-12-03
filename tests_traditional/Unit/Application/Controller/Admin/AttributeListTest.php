<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Attribute_List class
 */
class AttributeListTest extends \OxidTestCase
{

    /**
     * Attribute_List::Init() test case
     *
     * @return null
     */
    public function testInit()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $session->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oView = oxNew($this->getProxyClassName('Attribute_List'));
        $oView->init();

        $this->assertEquals("oxattribute", $oView->getNonPublicVar("_sListClass"));
    }

    /**
     * Attribute_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Attribute_List');
        $this->assertEquals('attribute_list.tpl', $oView->render());
    }
}
