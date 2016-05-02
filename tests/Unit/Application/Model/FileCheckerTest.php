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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Application\Model;

class FileCheckerTest extends \OxidTestCase
{

    /**
     * Testing version getter and setter
     */
    public function testGetVersion()
    {
        $oChecker = oxNew("oxFileChecker");
        $oChecker->setVersion("v123");

        $this->assertEquals("v123", $oChecker->getVersion());
    }

    /**
     * Testing edition getter and setter
     */
    public function testGetEdition()
    {
        $oChecker = oxNew("oxFileChecker");
        $oChecker->setEdition("e123");

        $this->assertEquals("e123", $oChecker->getEdition());
    }

    /**
     * Testing revision getter and setter
     */
    public function testGetRevision()
    {
        $oChecker = oxNew("oxFileChecker");
        $oChecker->setRevision("r123");

        $this->assertEquals("r123", $oChecker->getRevision());
    }

    /**
     * Testing base directory getter and setter
     */
    public function testGetBaseDirectory()
    {
        $oChecker = oxNew("oxFileChecker");
        $oChecker->setBaseDirectory("somedir");

        $this->assertEquals("somedir", $oChecker->getBaseDirectory());
    }


}