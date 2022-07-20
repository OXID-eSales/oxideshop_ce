<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Controller\Admin\ManufacturerPictures;
use OxidEsales\EshopCommunity\Application\Model\Manufacturer;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay;
use OxidEsales\EshopCommunity\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Core\UtilsObject;

/**
 * Tests for Manufacturer_Pictures class
 */
class ManufacturerPicturesTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->_oManufacturer = oxNew('oxManufacturer');
        $this->_oManufacturer->setId("_testManId");
        $this->_oManufacturer->save();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxmanufacturers');

        parent::tearDown();
    }

    /**
     * Manufacturer_Pictures::save() test case
     */
    public function testSaveAdditionalTest()
    {
        $this->getConfig()->setConfigParam('iPicCount', 0);

        $oView = $this->getMockBuilder(ManufacturerPictures::class)->onlyMethods(["resetContentCache"])->getMock();
        $oView->expects($this->once())->method('resetContentCache');

        $iCnt = 7;
        $this->getConfig()->setConfigParam('iPicCount', $iCnt);

        $oView->save();
    }

    /**
     * Manufacturer_Pictures::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", '_testManId');

        // testing..
        $oView = oxNew('Manufacturer_Pictures');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof Manufacturer);

        $this->assertEquals('manufacturer_pictures', $sTplName);
    }

    /**
     * Manufacturer_Pictures::deletePicture() test case - deleting thumbnail
     * 
     * @dataProvider setupSqlFilesProvider
     */
    public function testDeletePicture($picIndex)
    {
        $this->setRequestParameter("oxid", "_testManId");
        $this->setRequestParameter("masterPicIndex", $picIndex);

        $oManufacturerPic = oxNew('manufacturer_pictures');

        $this->_oManufacturer->oxmanufacturers__oxpic . $picIndex = new Field("testThumb" . $picIndex . "jpg");
        $this->_oManufacturer->save();

        $oManufacturerPic->deletePicture();

        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        $oUser->load('_testManId');
        $this->assertEquals('', $oUser->oxmanufacturers__oxthumb->value);
    }

    public function setupSqlFilesProvider()
    {
        return [
            [1],
            [2],
            [3]
        ];
    }

    /**
     * Manufacturer_Pictures::save() - in demo shop mode
     *
     * @return null
     */
    public function testSave_demoShopMode()
    {
        $oConfig = $this->getMockBuilder(Config::class)->onlyMethods(["isDemoShop"] )->getMock();
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(true);

        Registry::getSession()->deleteVariable("Errors");

        $oManufacturerPic = oxNew("Manufacturer_Pictures");
        Registry::set(Config::class, $oConfig);
        $oManufacturerPic->save();

        $aEx = Registry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);

        $this->assertTrue($oEx instanceof ExceptionToDisplay);
    }

    /**
     * Manufacturer_Pictures::deletePicture() - in demo shop mode
     *
     * @return null
     */
    public function testDeletePicture_demoShopMode()
    {
        $oConfig = $this->getMockBuilder(Config::class)->onlyMethods(["isDemoShop"] )->getMock();
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(true);

        Registry::getSession()->deleteVariable("Errors");

        $oManufacturerPic = oxNew("Manufacturer_Pictures");
        Registry::set(Config::class, $oConfig);
        $oManufacturerPic->deletePicture();

        $aEx = Registry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);

        $this->assertTrue($oEx instanceof ExceptionToDisplay);
    }

    /**
     * test for bug#0002041: editing inherited product pictures in subshop changes default shop for product
     */
    public function testSubshopStaysSame()
    {
        $oManufacturer = $this->getMockBuilder(Manufacturer::class)->onlyMethods(['load', 'save', 'assign'])->getMock();
        $oManufacturer->expects($this->once())->method('load')->with($this->equalTo('asdasdasd'))->will($this->returnValue(true));
        $oManufacturer->expects($this->once())->method('assign')->with($this->equalTo(array('s' => 'test')))->will($this->returnValue(null));
        $oManufacturer->expects($this->once())->method('save')->will($this->returnValue(null));

        UtilsObject::setClassInstance('oxmanufacturer', $oManufacturer);

        $this->setRequestParameter('oxid', 'asdasdasd');
        $this->setRequestParameter('editval', array('s' => 'test'));
        $oManufacturerPic = oxNew("Manufacturer_Pictures");
        $oManufacturerPic->save();
    }
}
