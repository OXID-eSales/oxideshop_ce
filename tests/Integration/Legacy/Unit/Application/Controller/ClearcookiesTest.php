<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxRegistry;

/**
 * Tests for content class
 */
class ClearcookiesTest extends \OxidTestCase
{
    protected $_oObj;

    /**
     * Test view render.
     */
    public function testRender()
    {
        $_SERVER['HTTP_COOKIE'] = "shop=1";

        $oView = oxNew('ClearCookies');

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ['setOxCookie']);
        $oUtilsServer
            ->method('setOxCookie')
            ->withConsecutive(
                ['shop'],
                ['language'],
                ['displayedCookiesNotification']
            );

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsServer::class, $oUtilsServer);

        $this->assertEquals('page/info/clearcookies', $oView->render());
    }

    /**
     * Testing Contact::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oView = oxNew('ClearCookies');
        $this->assertEquals(1, count($oView->getBreadCrumb()));
    }
}
