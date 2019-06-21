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
namespace OxidEsales\EshopCommunity\Tests\Integration\Seo;

use \OxidEsales\TestingLibrary\UnitTestCase;
use \OxidEsales\Facts\Facts;

use \OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use \OxidEsales\Eshop\Application\Model\Manufacturer;

/**
 * Class GetManufacturerSeoTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Seo
 */
class GetManufacturerSeoTest extends UnitTestCase
{
    /**
     * Sets up test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->setManufacturersPerPage(2);
    }

    /**
     * Test SeoEncoderManufacturer::getManufacturerPageUrl().
     * Test saving to database.
     */
    public function testGetManufacturerPageUrlFirstTimeWithSaving()
    {
        $seoEncoderManufacturer = oxNew(SeoEncoderManufacturer::class);

        $manufacturerPageUrl = $seoEncoderManufacturer->getManufacturerPageUrl(
            $this->getManufacturer(),
            2,  //page
            1   //languageId en
        );

        $this->assertEquals(
            $this->getExpectedManufacturerPageUrl(2),
            $manufacturerPageUrl
        );
    }


    /**
     * Test SeoEncoderManufacturer::getManufacturerPageUrl().
     * Test without saving to database.
     *
     * @depends testGetManufacturerPageUrlFirstTimeWithSaving
     */
    public function testGetManufacturerPageUrlSecondTimeWithoutSaving()
    {
        $seoEncoderManufacturer = $this->getMock(SeoEncoderManufacturer::class, ['_saveToDb']);
        $seoEncoderManufacturer->expects($this->never())->method('_saveToDb');

        $manufacturerPageUrl = $seoEncoderManufacturer->getManufacturerPageUrl(
            $this->getManufacturer(),
            2,  //page
            1   //languageId en
        );

        $this->assertEquals(
            $this->getExpectedManufacturerPageUrl(2),
            $manufacturerPageUrl
        );
    }

    /**
     * Returns Manufacturer
     *
     * @return  Manufacturer
     */
    private function getManufacturer()
    {
        $manufacturerId = $this->getManufacturerId();
        $manufacturer = oxNew(Manufacturer::class);
        $manufacturer->load($manufacturerId);

        return $manufacturer;
    }

    /**
     * Returns Manufacturer id
     *
     * @return string
     */
    private function getManufacturerId()
    {
        $manufacturerOxid = '9434afb379a46d6c141de9c9e5b94fcf';

        if (true === $this->isEnterpriseEdition()) {
            $manufacturerOxid = '2536d76675ebe5cb777411914a2fc8fb';
        }

        return $manufacturerOxid;
    }

    /**
     * Returns expected Manufacturer page URL
     *
     * @param  int $page
     * @return string
     */
    private function getExpectedManufacturerPageUrl($page)
    {
        $shopUrl        = $this->getConfig()->getCurrentShopUrl();
        $seoUrl         = $this->getSeoUrl();
        $pagePostfix    = '?pgNr=' . $page;

        return $shopUrl . $seoUrl . $pagePostfix;
    }

    /**
     * Returns seo URL
     *
     * @return string
     */
    private function getSeoUrl()
    {
        $seoUrl = 'en/By-manufacturer/Kuyichi/';

        if (true === $this->isEnterpriseEdition()) {
            $seoUrl = 'en/By-manufacturer/Manufacturer-2/';
        }

        return $seoUrl;
    }

    /**
     * @return bool
     */
    private function isEnterpriseEdition()
    {
        $facts = new Facts;

        return ('EE' === $facts->getEdition());
    }

    /**
     * Sets Manufacturers per page
     *
     * @param int $itemsPerPage
     */
    private function setManufacturersPerPage($itemsPerPage)
    {
        $this->setConfigParam("aNrofCatArticles", [$itemsPerPage]);
    }
}
