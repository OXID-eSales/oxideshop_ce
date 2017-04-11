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

require_once oxRegistry::getConfig()->getConfigParam('sCoreDir') . 'smarty/plugins/modifier.oxfilesize.php';


/**
 * Smarty modifier test case
 */
class Unit_Maintenance_oxfilesizeTest extends OxidTestCase
{

    /**
     * Byte result test
     *
     * @return null
     */
    public function testOxFileSizeBytes()
    {
        $iSize = 1023;
        $sRes = smarty_modifier_oxfilesize($iSize);
        $this->assertEquals("1023 B", $sRes);
    }

    /**
     * KiloByte result test
     *
     * @return null
     */
    public function testOxFileSizeKiloBytes()
    {
        $iSize = 1025;
        $sRes = smarty_modifier_oxfilesize($iSize);
        $this->assertEquals("1.0 KB", $sRes);
    }

    /**
     * MegaByte result test
     *
     * @return null
     */
    public function testOxFileSizeMegaBytes()
    {
        $iSize = 1024 * 1024 * 1.1;
        $sRes = smarty_modifier_oxfilesize($iSize);

        $this->assertEquals("1.1 MB", $sRes);
    }

    /**
     * GigaByte result test
     *
     * @return null
     */
    public function testOxFileSizeGigaBytes()
    {
        $iSize = 1024 * 1024 * 1024 * 1.3;
        $sRes = smarty_modifier_oxfilesize($iSize);

        $this->assertEquals("1.3 GB", $sRes);
    }


}