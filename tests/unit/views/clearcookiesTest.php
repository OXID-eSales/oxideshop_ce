<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for content class
 */
class Unit_Views_clearcookiesTest extends OxidTestCase
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


        $oView = new clearcookies();

        $oUtilsServer = $this->getMock('oxUtilsServer', array('setOxCookie'));
        $oUtilsServer->expects($this->at(0))->method('setOxCookie')->with($this->equalTo('shop'));
        $oUtilsServer->expects($this->at(1))->method('setOxCookie')->with($this->equalTo('language'));
        $oUtilsServer->expects($this->at(2))->method('setOxCookie')->with($this->equalTo('displayedCookiesNotification'));
        oxRegistry::set('oxUtilsServer', $oUtilsServer);

        $this->assertEquals('page/info/clearcookies.tpl', $oView->render());
    }

    /**
     * Testing Contact::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oView = new clearcookies();
        $this->assertEquals(1, count($oView->getBreadCrumb()));
    }
}