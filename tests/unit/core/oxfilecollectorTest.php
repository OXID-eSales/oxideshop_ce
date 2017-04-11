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

class Unit_Core_oxFileCollectorTest extends OxidTestCase
{

    /**
     * Testing directory file list collecting     *
     */
    public function testAddDirectoryFilesWithExtensions()
    {
        //TODO check adding directories recursively

        $oDirReader = oxNew("oxFileCollector");
        $oDirReader->setBaseDirectory(oxRegistry::getConfig()->getConfigParam("sShopDir"));

        $oDirReader->addDirectoryFiles('bin/', array('php', 'tpl'));
        $aResultExistingPHP = $oDirReader->getFiles();

        $this->assertEquals(1, count($aResultExistingPHP));
        $this->assertContains('bin/cron.php', $aResultExistingPHP);
    }

    /**
     * Testing directory file list collecting     *
     */
    public function testAddDirectoryFilesWithoutExtensions()
    {
        $oDirReader = oxNew("oxFileCollector");
        $oDirReader->setBaseDirectory(oxRegistry::getConfig()->getConfigParam("sShopDir"));

        $oDirReader->addDirectoryFiles('bin/');
        $aResultExistingAll = $oDirReader->getFiles();

        $this->assertEquals(3, count($aResultExistingAll));
        $this->assertContains('bin/.htaccess', $aResultExistingAll);
        $this->assertContains('bin/cron.php', $aResultExistingAll);
        $this->assertContains('bin/log.txt', $aResultExistingAll);

    }

    /**
     * Testing adding files to collection     *
     */
    public function testAddFile()
    {
        $oDirReader = oxNew("oxFileCollector");
        $oDirReader->setBaseDirectory(oxRegistry::getConfig()->getConfigParam("sShopDir"));

        $oDirReader->addFile('index.php');
        $oDirReader->addFile('bin/nofile.php');
        $oDirReader->addFile('bin/cron.php');
        $aResult = $oDirReader->getFiles();

        $this->assertEquals(2, count($aResult));
        $this->assertContains('bin/cron.php', $aResult);
        $this->assertContains('index.php', $aResult);
    }

}