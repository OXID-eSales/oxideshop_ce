<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\EshopCommunity\Tests\Utils\Database\FixtureLoader;
use Webmozart\PathUtil\Path;

require_once Path::join(dirname(__DIR__, 2), 'bootstrap.php');
$path1 = Path::join(dirname(__DIR__, 1), '_data', 'dump-2.sql');
$path2 = Path::join(dirname(__DIR__, 1), '_data', 'dump-3.sql');
\OxidEsales\EshopCommunity\Tests\Utils\Database\TestDatabaseHandler::createDatabase();
\OxidEsales\EshopCommunity\Tests\Utils\Database\TestDatabaseHandler::init();



FixtureLoader::init(\OxidEsales\EshopCommunity\Tests\Utils\Database\TestDatabaseHandler::get());
$fixtureLoader = FixtureLoader::getInstance();
$fixtureLoader->loadFixtures([Path::join(dirname(__DIR__, 1), '_data',  'db_fixture.yml')]);



\OxidEsales\EshopCommunity\Tests\Utils\Database\TestDatabaseHandler::createDump($path1, $path2);


// This is acceptance bootstrap
$helper = new \OxidEsales\Codeception\Module\FixturesHelper();
$helper->loadRuntimeFixtures(dirname(__FILE__) . '/../_data/fixtures.php');
$helper->loadRuntimeFixtures(dirname(__FILE__) . '/../_data/voucher.php');
