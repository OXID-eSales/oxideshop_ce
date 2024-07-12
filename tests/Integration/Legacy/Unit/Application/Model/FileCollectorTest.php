<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

class FileCollectorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing directory file list collecting     *
     */
    public function testAddDirectoryFilesWithExtensions()
    {
        //TODO check adding directories recursively

        $oDirReader = oxNew("oxFileCollector");
        $oDirReader->setBaseDirectory($this->getConfig()->getConfigParam("sShopDir"));

        $oDirReader->addDirectoryFiles('bin/', ['php', 'tpl']);

        $aResultExistingPHP = $oDirReader->getFiles();

        $this->assertCount(1, $aResultExistingPHP);
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

        $this->assertCount(3, $aResultExistingAll);
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

        $this->assertCount(2, $aResult);
        $this->assertContains('bin/cron.php', $aResult);
        $this->assertContains('index.php', $aResult);
    }
}
