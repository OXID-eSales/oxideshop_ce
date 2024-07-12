<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxTestModules;

/**
 * Testing moredetails class
 */
class MoredetailsTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
        oxTestModules::addFunction('oxSeoEncoderManufacturer', '_saveToDb', '{return null;}');
    }

    /**
     * Test get product id's.
     */
    public function testGetProductId()
    {
        $oMoreDetails = $this->getProxyClass('moredetails');
        $this->setRequestParameter('anid', '2000');
        $oMoreDetails->init();

        $this->assertEquals('2000', $oMoreDetails->getProductId());
    }

    /**
     * Test get product.
     */
    public function testGetProduct()
    {
        $oMoreDetails = $this->getProxyClass('moredetails');
        $this->setRequestParameter('anid', '2000');
        $oMoreDetails->init();

        $this->assertEquals('2000', $oMoreDetails->getProduct()->getId());
    }

    /**
     * Test get active picture id.
     */
    public function testGetActPictureId()
    {
        $oMoreDetails = $this->getProxyClass('moredetails');
        $this->setRequestParameter('anid', '096a1b0849d5ffa4dd48cd388902420b');
        $oMoreDetails->init();

        $this->assertEquals('1', $oMoreDetails->getActPictureId());
    }

    /**
     * Test get product zoom pictures.
     */
    public function testGetArtZoomPics()
    {
        $oMoreDetails = $this->getProxyClass('moredetails');
        $this->setRequestParameter('anid', '096a1b0849d5ffa4dd48cd388902420b');
        $oMoreDetails->init();
        $aZoom = $oMoreDetails->getArtZoomPics();

        $this->assertEquals('front_z1(1).jpg', basename((string) $aZoom[1]['file']));
    }
}
