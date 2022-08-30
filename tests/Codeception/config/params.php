<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Facts\Config\ConfigFile;
use OxidEsales\Facts\Facts;
use OxidEsales\Codeception\Module\Database\DatabaseDefaultsFileGenerator;

$facts = new Facts();

$selenium_server_port = getenv('SELENIUM_SERVER_PORT');
$selenium_server_port = ($selenium_server_port) ? : '4444';
$selenium_server_host = getenv('SELENIUM_SERVER_HOST');
$selenium_server_host = ($selenium_server_host) ? : '127.0.0.1';
$php = (getenv('PHPBIN')) ? : 'php';
$cc_screen_shot_url = getenv('CC_SCREEN_SHOTS_URL');
$cc_screen_shot_url = ($cc_screen_shot_url) ? : '';
$browser = getenv('BROWSER_NAME');
$browser = ($browser) ? : 'firefox';

return [
    'SHOP_URL' => $facts->getShopUrl(),
    'SHOP_SOURCE_PATH' => $facts->getSourcePath(),
    'VENDOR_PATH' => $facts->getVendorPath(),
    'DB_NAME' => $facts->getDatabaseName(),
    'DB_USERNAME' => $facts->getDatabaseUserName(),
    'DB_PASSWORD' => $facts->getDatabasePassword(),
    'DB_HOST' => $facts->getDatabaseHost(),
    'DB_PORT' => $facts->getDatabasePort(),
    'DUMP_PATH' => getTestDataDumpFilePath(),
    'FIXTURES_PATH' => getTestFixtureSqlFilePath(),
    'MYSQL_CONFIG_PATH' => getMysqlConfigPath(),
    'SELENIUM_SERVER_PORT' => $selenium_server_port,
    'SELENIUM_SERVER_HOST' => $selenium_server_host,
    'PHP_BIN' => $php,
    'SCREEN_SHOT_URL' => $cc_screen_shot_url,
    'BROWSER' => $browser
];

function getTestDataDumpFilePath()
{
    return getShopTestPath() . '/Codeception/_data/generated/shop-dump.sql';
}

function getTestFixtureSqlFilePath()
{
    return getShopTestPath() . '/Codeception/_data/dump.sql';
}

function getShopSuitePath($facts)
{
    $testSuitePath = getenv('TEST_SUITE');
    if (!$testSuitePath) {
        $testSuitePath = $facts->getShopRootPath() . '/tests';
    }
    return $testSuitePath;
}

function getShopTestPath()
{
    $facts = new Facts();

    if ($facts->isEnterprise()) {
        $shopTestPath = $facts->getEnterpriseEditionRootPath() . '/Tests';
    } else {
        $shopTestPath = getShopSuitePath($facts);
    }
    return $shopTestPath;
}

function getMysqlConfigPath()
{
    $configFile = new ConfigFile();

    $generator = new DatabaseDefaultsFileGenerator($configFile);

    return $generator->generate();
}
