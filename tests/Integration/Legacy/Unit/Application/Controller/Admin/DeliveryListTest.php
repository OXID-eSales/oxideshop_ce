<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Delivery_List class
 */
class DeliveryListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Delivery_List::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $session->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oView = oxNew($this->getProxyClassName('Delivery_List'));
        $oView->init();

        $this->assertSame('oxdelivery', $oView->getNonPublicVar("_sListClass"));
        $this->assertSame('oxdeliverylist', $oView->getNonPublicVar("_sListType"));
        $this->assertSame(["oxdelivery" => ["oxsort" => "asc"]], $oView->getListSorting());
        $this->assertSame('delivery_list', $oView->render());
    }
}
