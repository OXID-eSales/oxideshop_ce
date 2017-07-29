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

namespace OxidEsales\EshopCommunity\Tests\Acceptance;

abstract class FrontendTestCase extends AcceptanceTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->callShopSC("oxConfig", null, null, array(
            "iTopNaviCatCount" => array(
                "type" => "str",
                "value" => '3',
                "module" => "theme:azure"
            ),
            "aNrofCatArticles" => array(
                "type" => "arr",
                "value" => 'a:6:{i:0;s:2:"10";i:1;s:2:"20";i:2;s:2:"50";i:3;s:3:"100";i:4;s:1:"2";i:5;s:1:"1";}',
                "module" => "theme:azure"
            ),
            "aNrofCatArticlesInGrid" => array(
                "type" => "arr",
                "value" => 'a:4:{i:0;s:2:"12";i:1;s:2:"16";i:2;s:2:"24";i:3;s:2:"32";}',
                "module" => "theme:azure"
            )
        ));
    }
}
