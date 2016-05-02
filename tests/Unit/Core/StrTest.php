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
namespace Unit\Core;

use oxStr;
use oxStrMb;

class StrTest extends \OxidTestCase
{

    public function testGetStrHandler()
    {
        $oStr = $this->getProxyClass('oxStr');

        $this->getConfig()->setConfigParam('iUtfMode', 0);
        $this->assertFalse($oStr->UNITgetStrHandler() instanceof oxStrMb);

        $this->getConfig()->setConfigParam('iUtfMode', 1);
        $this->assertTrue($oStr->UNITgetStrHandler() instanceof oxStrMb);
    }

    public function testGetStr()
    {
        if ($this->getConfig()->getConfigParam('iUtfMode')) {
            $this->assertTrue(oxStr::getStr() instanceof oxStrMb);
        } else {
            $this->assertFalse(oxStr::getStr() instanceof oxStrMb);
        }
    }
}
