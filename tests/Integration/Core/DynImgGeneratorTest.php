<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use oxDynImgGenerator;

/**
 * Tests for Dynamic Image generation
 */
class DynImgGeneratorTest extends \OxidTestCase
{
    /**
     * When a non-existent image is requested a jpeg image named nopic.jpeg is returned instead,
     * Test for the proper HTTP status code
     */
    public function testRequestNonExistentImageReturnsProperHttpStatusCode()
    {
        $shopUrl = $this->getConfig()->getShopUrl(1);
        $command = 'curl -I -s ' . $shopUrl . '/out/pictures/generated/product/1/87_87_75/wrongname.JPEG';
        $response = shell_exec($command);

        $this->assertNotNull($response, 'This command failed to execute: ' . $command);
        $this->assertContains('HTTP/1.1 404 Not Found', $response, 'When an image file is not found the HTTP status code 404 is returned');
    }

    /**
     * When a non-existent image is requested a jpeg image named nopic.jpeg is returned instead,
     * Test for the proper content type header.
     */
    public function testRequestNonExistentImageReturnsProperContentHeader()
    {
        $shopUrl = $this->getConfig()->getShopUrl(1);
        $command = 'curl -I -s ' . $shopUrl . '/out/pictures/generated/product/1/87_87_75/wrongname.png';
        $response = shell_exec($command);

        $this->assertNotNull($response, 'This command failed to execute: ' . $command);
        $this->assertContains(strtolower('Content-Type: image/jpeg;'), strtolower($response), '');
    }
}
