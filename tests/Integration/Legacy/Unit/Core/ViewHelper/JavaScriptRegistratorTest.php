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

use OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator;
use OxidEsales\Eshop\Core\Registry;

/**
 * Testing JavaScriptRegistrator class
 */
class JavaScriptRegistratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider addFileProvider
     */
    public function testAddFile($file, $expected)
    {
        $utils = $this->getMock('oxUtilsUrl', ['getHosts']);
        $utils->method('getHosts')->willReturn([
            'shopurl.de'
        ]);
        Registry::set('oxUtilsUrl', $utils);

        $scriptRegistrator = $this->getMock(JavaScriptRegistrator::class, ['getFileModificationTime']);
        $scriptRegistrator->method('getFileModificationTime')->willReturn(123456789);

        $scriptRegistrator->addFile($file, 0);

        $this->assertEquals($expected, Registry::getConfig()->getGlobalParameter('includes')[0][0]);
    }

    public function addFileProvider(): \Iterator
    {
        yield ['http://someurl/script.js', 'http://someurl/script.js'];
        yield ['http://someurl/script.js', 'http://someurl/script.js'];
        yield ['http://shopurl.de/script.js', 'http://shopurl.de/script.js?123456789'];
        yield ['https://shopurl.de/script.js', 'https://shopurl.de/script.js?123456789'];
        yield ['http://shopurl.de/script.js', 'http://shopurl.de/script.js?123456789'];
        yield ['https://shopurl.de/script.js', 'https://shopurl.de/script.js?123456789'];
        yield ['//shopurl.de/script.js', '//shopurl.de/script.js?123456789'];
        yield ['//shopurl.de/script.js', '//shopurl.de/script.js?123456789'];
    }
}
