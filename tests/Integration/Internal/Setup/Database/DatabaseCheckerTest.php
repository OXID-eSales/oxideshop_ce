<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseChecker;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Facts\Config\ConfigFile;
use PHPUnit\Framework\TestCase;

class DatabaseCheckerTest extends TestCase
{
    use ContainerTrait;

    public function testCheckIfDatabaseExistsAndNotEmpty(): void
    {
        $configFile = new ConfigFile();
        $dbHost = $configFile->getVar('dbHost');
        $dbPort =  $configFile->getVar('dbPort');
        $dbUser = $configFile->getVar('dbUser');
        $dbPwd = $configFile->getVar('dbPwd');
        $dbName = $configFile->getVar('dbName');

        $basicContext = $this->get(BasicContextInterface::class);
        $databaseChecker = new DatabaseChecker($basicContext);

        $this->assertTrue(
            $databaseChecker->checkIfDatabaseExistsAndNotEmpty($dbHost, $dbPort, $dbUser, $dbPwd, $dbName)
        );

        $this->assertFalse(
            $databaseChecker->checkIfDatabaseExistsAndNotEmpty($dbHost, $dbPort, $dbUser, $dbPwd, 'testCheckIfDbExistsAndNotEmpty')
        );
    }
}
