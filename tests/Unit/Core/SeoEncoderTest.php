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
namespace Unit\Core;

use \oxRegistry;

class SeoEncoderTest extends \OxidTestCase
{

    /**
     * Test case for oxSeoEncoder::addLanguageParam()
     * Link to the bug: https://bugs.oxid-esales.com/view.php?id=6407
     */
    public function testAddLanguageParamForBug6407()
    {
        $baseId = 2;
        $oLang = $this->getMock('oxlang', array('getLanguageIds'));
        $oLang
            ->expects($this->any())
            ->method('getLanguageIds')
            ->will($this->returnValue(array($baseId => 'en_US')));
        oxRegistry::set('oxLang', $oLang);

        $sUrl = "Angebote/Transportcontainer-THE-BARREL.html";
        $oEncoder = oxNew('oxSeoEncoder');

        // The addLanguageParam() method should add the language code to the uri only once irrespective of the number of times the method gets called.
        // Hence calling the same method twice in the below code.
        $sUri = $oEncoder->_prepareUri($oEncoder->addLanguageParam($sUrl, $baseId), $baseId);
        $this->assertEquals("en-US/Angebote/Transportcontainer-THE-BARREL.html", $sUri);

        $sUri = $oEncoder->_prepareUri($oEncoder->addLanguageParam($sUrl, $baseId), $baseId);
        $this->assertEquals("en-US/Angebote/Transportcontainer-THE-BARREL.html", $sUri);
    }
}
