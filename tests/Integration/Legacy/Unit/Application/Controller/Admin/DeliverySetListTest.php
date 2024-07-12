<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for DeliverySet_List class
 */
class DeliverySetListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * DeliverySet_List::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $session->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oView = oxNew($this->getProxyClassName('DeliverySet_List'));
        $oView->init();

        $this->assertEquals('oxdeliveryset', $oView->getNonPublicVar("_sListClass"));
        $this->assertEquals('oxdeliverysetlist', $oView->getNonPublicVar("_sListType"));
        $this->assertEquals(["oxdeliveryset" => ["oxpos" => "asc"]], $oView->getListSorting());
        $this->assertEquals('deliveryset_list', $oView->render());
    }
}
