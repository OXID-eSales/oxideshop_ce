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
    'SHOP_URL' => getenv('SHOP_URL') ?: $facts->getShopUrl(),
    'SHOP_SOURCE_PATH' => getenv('SHOP_SOURCE_PATH') ?: $facts->getSourcePath(),
    'VENDOR_PATH' => $facts->getVendorPath(),
    'DB_NAME' => getenv('DB_NAME') ?: $facts->getDatabaseName(),
    'DB_USERNAME' => getenv('DB_USERNAME') ?: $facts->getDatabaseUserName(),
    'DB_PASSWORD' => getenv('DB_PASSWORD') ?: $facts->getDatabasePassword(),
    'DB_HOST' => getenv('DB_HOST') ?: $facts->getDatabaseHost(),
    'DB_PORT' => getenv('DB_PORT') ?: $facts->getDatabasePort(),
    'DUMP_PATH' => getTestDataDumpFilePath(),
    'FIXTURES_PATH' => getTestFixtureSqlFilePath(),
    'MYSQL_CONFIG_PATH' => getMysqlConfigPath(),
    'SELENIUM_SERVER_PORT' => getenv('SELENIUM_SERVER_PORT') ?: '4444',
    'SELENIUM_SERVER_HOST' => getenv('SELENIUM_SERVER_HOST') ?: '127.0.0.1',
    'PHP_BIN' => (getenv('PHPBIN')) ?: 'php',
    'SCREEN_SHOT_URL' => getenv('CC_SCREEN_SHOTS_URL') ?: '',
    'BROWSER' => getenv('BROWSER_NAME') ?: 'firefox',
    'THEME_ID' => getenv('THEME_ID') ?: 'apex',
];

function getTestDataDumpFilePath(): string
{
    return getShopTestPath() . '/Codeception/Support/_generated/shop-dump.sql';
}

function getTestFixtureSqlFilePath(): string
{
    return getShopTestPath() . '/Codeception/Support/Data/dump.sql';
}

function getShopSuitePath(Facts $facts): string
{
    $testSuitePath = (string) getenv('TEST_SUITE');
    if ($testSuitePath === '' || $testSuitePath === '0') {
        $testSuitePath = $facts->getShopRootPath() . '/tests';
    }
    return $testSuitePath;
}

function getShopTestPath(): string
{
    $facts = new Facts();
    return $facts->isEnterprise()
        ? $facts->getEnterpriseEditionRootPath() . '/Tests'
        : getShopSuitePath($facts);
}

function getMysqlConfigPath(): string
{
    $configFile = new ConfigFile();

    return (new DatabaseDefaultsFileGenerator($configFile))->generate();
}
