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

namespace Unit\Core\ViewHelper;

use OxidEsales\Eshop\Core\ViewHelper\StyleRegistrator;
use OxidEsales\Eshop\Core\Registry;

/**
 * Testing StyleRegistrator class
 */
class StyleRegistratorTest extends \OxidTestCase
{
    /**
     * @dataProvider addFileProvider
     */
    public function testAddFile($file, $shopurl, $expected)
    {
        $config = $this->getMock('oxConfig', ['getCurrentShopUrl']);
        $config->expects($this->any())->method('getCurrentShopUrl')->will($this->returnValue($shopurl));
        Registry::set('oxConfig', $config);

        $styleRegistrator = $this->getMock(StyleRegistrator::class, ['getFileModificationTime']);
        $styleRegistrator->expects($this->any())->method('getFileModificationTime')->will($this->returnValue(123456789));

        $styleRegistrator->addFile($file, false ,false);

        $this->assertEquals($expected, Registry::getConfig()->getGlobalParameter('styles')[0]);
    }

    public function addFileProvider()
    {
        return [
            ['http://someurl/style.css', 'http://shopurl.de', 'http://someurl/style.css'],
            ['http://someurl/style.css', 'http://shopurl.de', 'http://someurl/style.css'],
            ['http://shopurl.de/style.css', 'http://shopurl.de', 'http://shopurl.de/style.css?123456789'],
            ['https://shopurl.de/style.css', 'https://shopurl.de', 'https://shopurl.de/style.css?123456789'],
            ['http://shopurl.de/style.css', 'https://shopurl.de', 'http://shopurl.de/style.css?123456789'],
            ['https://shopurl.de/style.css', 'http://shopurl.de', 'https://shopurl.de/style.css?123456789'],
            ['//shopurl.de/style.css', 'http://shopurl.de', '//shopurl.de/style.css?123456789'],
            ['//shopurl.de/style.css', 'https://shopurl.de', '//shopurl.de/style.css?123456789'],
        ];
    }
}
