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

use OxidEsales\EshopCommunity\Application\Model\SeoEncoderVendor;

use \Exception;
use \oxField;
use \oxDb;
use \oxTestModules;

/**
 * Tests for Vendor_Seo class
 */
class VendorSeoTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sQ = "delete from oxvendor where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        parent::tearDown();
    }

    /**
     * Vendor_Seo::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Vendor_Seo');
        $this->assertEquals('object_seo.tpl', $oView->render());
    }

    /**
     * Vendor_Seo::GetType() test case
     *
     * @return null
     */
    public function testGetType()
    {
        // testing..
        $oView = oxNew('Vendor_Seo');
        $this->assertEquals("oxvendor", $oView->UNITgetType());
    }

    /**
     * Vendor_Seo::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        $this->setRequestParameter('oxid', "testId");

        oxTestModules::addFunction('oxbase', 'save', '{ throw new Exception("save"); }');
        oxTestModules::addFunction('oxbase', 'load', '{ return true; }');

        // testing..
        try {
            $oView = oxNew('Vendor_Seo');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in Vendor_Seo::save()");

            return;
        }
        $this->fail("Error in Vendor_Seo::save()");
    }

    /**
     * Vendor_Seo::_getEncoder() test case
     *
     * @return null
     */
    public function testGetEncoder()
    {
        $oView = oxNew('Vendor_Seo');
        $this->assertTrue($oView->UNITgetEncoder() instanceof SeoEncoderVendor);
    }

    /**
     * Vendor_Seo::isSuffixSupported() test case
     *
     * @return null
     */
    public function testIsSuffixSupported()
    {
        $oView = oxNew('Vendor_Seo');
        $this->assertTrue($oView->isSuffixSupported());
    }

    /**
     * Vendor_Seo::isEntrySuffixed() test case
     *
     * @return null
     */
    public function testIsEntrySuffixed()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId("_test1");
        $oVendor->oxvendor__oxshowsuffix = new oxField(1);
        $oVendor->save();

        $oVendor = oxNew('oxVendor');
        $oVendor->setId("_test2");
        $oVendor->oxvendor__oxshowsuffix = new oxField(0);
        $oVendor->save();


        $oView = $this->getMock("Vendor_Seo", array("getEditObjectId"));
        $oView->expects($this->at(0))->method('getEditObjectId')->will($this->returnValue("_test1"));
        $oView->expects($this->at(1))->method('getEditObjectId')->will($this->returnValue("_test2"));
        $this->assertTrue($oView->isEntrySuffixed());
        $this->assertFalse($oView->isEntrySuffixed());
    }

    /**
     * Vendor_Seo::getEntryUri() test case
     *
     * @return null
     */
    public function testGetEntryUri()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId("_test1");
        $oVendor->oxvendor__oxshowsuffix = new oxField(0);
        $oVendor->save();

        $oEncoder = $this->getMock("oxSeoEncoderVendor", array("getVendorUri"));
        $oEncoder->expects($this->once())->method('getVendorUri')->will($this->returnValue("VendorUri"));

        $oView = $this->getMock("Vendor_Seo", array("getEditObjectId", "_getEncoder"));
        $oView->expects($this->once())->method('getEditObjectId')->will($this->returnValue("_test1"));
        $oView->expects($this->once())->method('_getEncoder')->will($this->returnValue($oEncoder));
        $this->assertEquals("VendorUri", $oView->getEntryUri());
    }

    /**
     * Vendor_Seo::_getStdUrl() test case
     *
     * @return null
     */
    public function testGetStdUrl()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId("_test1");
        $oVendor->oxvendor__oxshowsuffix = new oxField(0);
        $oVendor->save();

        $oView = $this->getMock("Vendor_Seo", array("getEditLang"));
        $oView->expects($this->once())->method('getEditLang')->will($this->returnValue(0));

        $this->assertEquals($oVendor->getBaseStdLink(0, true, false), $oView->UNITgetStdUrl("_test1"));
    }
}
