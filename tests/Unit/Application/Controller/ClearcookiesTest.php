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
    protected $_oObj = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test view render.
     *
     * @return null
     */
    public function testRender()
    {
        $_SERVER['HTTP_COOKIE'] = "shop=1";

        $oView = oxNew('ClearCookies');

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, array('setOxCookie'));
        $oUtilsServer->expects($this->at(0))->method('setOxCookie')->with($this->equalTo('shop'));
        $oUtilsServer->expects($this->at(1))->method('setOxCookie')->with($this->equalTo('language'));
        $oUtilsServer->expects($this->at(2))->method('setOxCookie')->with($this->equalTo('displayedCookiesNotification'));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsServer::class, $oUtilsServer);

        $this->assertEquals('page/info/clearcookies.tpl', $oView->render());
    }

    /**
     * Testing Contact::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oView = oxNew('ClearCookies');
        $this->assertEquals(1, count($oView->getBreadCrumb()));
    }
}
