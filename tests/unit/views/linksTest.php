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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Testing links class
 */
class Unit_Views_linksTest extends OxidTestCase
{

    /**
     * Test get link list.
     *
     * @return null
     */
    public function testGetLinksList()
    {
        $oLinks = $this->getProxyClass('links');
        $oLink = $oLinks->getLinksList()->current();
        $this->assertEquals('http://www.oxid-esales.com', $oLink->oxlinks__oxurl->value);
    }

    /**
     * Testing Links::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oLinks = new Links();
        $aResult = array();
        $aResults = array();

        $aResult["title"] = oxRegistry::getLang()->translateString('LINKS', oxRegistry::getLang()->getBaseLanguage(), false);
        $aResult["link"] = $oLinks->getLink();

        $aResults[] = $aResult;

        $this->assertEquals($aResults, $oLinks->getBreadCrumb());
    }
}
