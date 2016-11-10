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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\SeoEncoderContent;
use \oxDb;

/**
 * Tests for Content_Seo class
 */
class ContentSeoTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sQ = "delete from oxcontents where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        parent::tearDown();
    }

    /**
     * Content_Seo::GetType() test case
     *
     * @return null
     */
    public function testGetType()
    {
        // testing..
        $oView = oxNew('Content_Seo');
        $this->assertEquals('oxcontent', $oView->UNITgetType());
    }

    /**
     * Content_Seo::_getEncoder() test case
     *
     * @return null
     */
    public function testGetEncoder()
    {
        $oView = oxNew('Content_Seo');
        $this->assertTrue($oView->UNITgetEncoder() instanceof SeoEncoderContent);
    }

    /**
     * Content_Seo::getEntryUri() test case
     *
     * @return null
     */
    public function testGetEntryUri()
    {
        $oContent = oxNew('oxContent');
        $oContent->setId("_test1");
        $oContent->save();

        $oEncoder = $this->getMock("oxSeoEncoderContent", array("getContentUri"));
        $oEncoder->expects($this->once())->method('getContentUri')->will($this->returnValue("ContentUri"));

        $oView = $this->getMock("Content_Seo", array("getEditObjectId", "_getEncoder"));
        $oView->expects($this->once())->method('getEditObjectId')->will($this->returnValue("_test1"));
        $oView->expects($this->once())->method('_getEncoder')->will($this->returnValue($oEncoder));
        $this->assertEquals("ContentUri", $oView->getEntryUri());
    }

    /**
     * Content_Seo::_getEncoder() test case
     *
     * @return null
     */
    public function testGetStdUrl()
    {
        $oContent = oxNew('oxContent');
        $oContent->setId("_test1");
        $oContent->save();

        $oView = $this->getMock("Content_Seo", array("getEditLang"));
        $oView->expects($this->once())->method('getEditLang')->will($this->returnValue(0));

        $this->assertEquals($oContent->getBaseStdLink(0, true, false), $oView->UNITgetStdUrl("_test1"));
    }

    /**
     * Content_Seo::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Content_Seo');
        $this->assertEquals('object_seo.tpl', $oView->render());
    }
}
