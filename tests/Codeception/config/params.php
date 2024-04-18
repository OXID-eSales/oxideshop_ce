<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Facts\Config\ConfigFile;
use OxidEsales\Facts\Facts;
use OxidEsales\Codeception\Module\Database\DatabaseDefaultsFileGenerator;
use Symfony\Component\Filesystem\Path;

if ($shopRootPath = getenv('SHOP_ROOT_PATH')){
    require_once(Path::join($shopRootPath, 'source', 'bootstrap.php'));
}

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
    return getShopTestPath() . '/Codeception/_data/generated/shop-dump.sql';
}

function getTestFixtureSqlFilePath(): string
{
    return getShopTestPath() . '/Codeception/_data/dump.sql';
}

function getTestSetupSqlFilePath(): string
{
    return getShopTestPath() . '/Codeception/_data/setup_dump.sql';
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
    $facts = new Facts();
    $configFilePath = Path::join($facts->getSourcePath(), 'config.inc.php');
    $configFile = new ConfigFile($configFilePath);

    return (new DatabaseDefaultsFileGenerator($configFile))->generate();
}
