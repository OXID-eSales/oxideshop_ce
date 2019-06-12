<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

class FileCollectorTest extends \OxidTestCase
{

    /**
     * Testing directory file list collecting     *
     */
    public function testAddDirectoryFilesWithExtensions()
    {
        //TODO check adding directories recursively

        $oDirReader = oxNew("oxFileCollector");
        $oDirReader->setBaseDirectory($this->getConfig()->getConfigParam("sShopDir"));

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
        $oDirReader->setBaseDirectory($this->getConfig()->getConfigParam("sShopDir"));

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
        $oDirReader->setBaseDirectory($this->getConfig()->getConfigParam("sShopDir"));

        $oDirReader->addFile('index.php');
        $oDirReader->addFile('bin/nofile.php');
        $oDirReader->addFile('bin/cron.php');
        $aResult = $oDirReader->getFiles();

        $this->assertEquals(2, count($aResult));
        $this->assertContains('bin/cron.php', $aResult);
        $this->assertContains('index.php', $aResult);
    }
}
