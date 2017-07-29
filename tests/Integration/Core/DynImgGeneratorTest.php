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
