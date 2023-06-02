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
    'SELENIUM_SERVER_PORT' => getenv('SELENIUM_SERVER_PORT') ?: '4444',
    'SELENIUM_SERVER_HOST' => getenv('SELENIUM_SERVER_HOST') ?: 'selenium',
    'PHP_BIN' => (getenv('PHPBIN')) ?: 'php',
    'SCREEN_SHOT_URL' => getenv('CC_SCREEN_SHOTS_URL') ?: '',
    'BROWSER' => getenv('BROWSER_NAME') ?: 'chrome',
    'THEME_ID' => getenv('THEME_ID') ?: 'apex',
];

function getTestDataDumpFilePath(): string
{
    return getShopTestPath() . '/Codeception/_data/generated/shop-dump.sql';
}

function getTestFixtureSqlFilePath(): string
{
    return getShopTestPath() . '/Codeception/_data/dump.sql';
}

function getShopSuitePath(Facts $facts): string
{
    $testSuitePath = (string) getenv('TEST_SUITE');
    if (!$testSuitePath) {
        $testSuitePath = $facts->getShopRootPath() . '/tests';
    }
    return $testSuitePath;
}

function getShopTestPath(): string
{
    $facts = new Facts();

    if ($facts->isEnterprise()) {
        $shopTestPath = $facts->getEnterpriseEditionRootPath() . '/Tests';
    } else {
        $shopTestPath = getShopSuitePath($facts);
    }
    return $shopTestPath;
}

function getMysqlConfigPath(): string
{
    $configFile = new ConfigFile();

    return (new DatabaseDefaultsFileGenerator($configFile))->generate();
}
