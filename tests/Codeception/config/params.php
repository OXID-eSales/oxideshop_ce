<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\DoctrineMigrationWrapper;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\Utils\Database\TestDatabaseHandler;
use OxidEsales\Facts\Facts;
use Webmozart\PathUtil\Path;

require_once Path::join(dirname(__DIR__, 2), 'bootstrap.php');
TestDatabaseHandler::setupTestConfigInc();

$facts = new Facts();

$selenium_server_port = getenv('SELENIUM_SERVER_PORT');
$selenium_server_port = ($selenium_server_port) ? : '4444';
$selenium_server_host = getenv('SELENIUM_SERVER_HOST');
$selenium_server_host = ($selenium_server_host) ? : '127.0.0.1';
$php = (getenv('PHPBIN')) ? : 'php';
$cc_screen_shot_url = getenv('CC_SCREEN_SHOTS_URL');
$cc_screen_shot_url = ($cc_screen_shot_url) ? : '';

$config = Registry::getConfig();
return [
    'SHOP_URL' => $facts->getShopUrl(),
    'SHOP_SOURCE_PATH' => $facts->getSourcePath(),
    'VENDOR_PATH' => $facts->getVendorPath(),
    'DB_NAME' => $config->getConfigParam('dbName'),
    'DB_USERNAME' => $config->getConfigParam('dbUser'),
    'DB_PASSWORD' => $config->getConfigParam('dbPwd'),
    'DB_HOST' => $config->getConfigParam('dbHost'),
    'DB_PORT' => $config->getConfigParam('dbPort'),
    'DUMP_PATH' => Path::join('..', '..', 'TestUtils', 'Database', 'in_memory_schema_ce.sql'),
    'MYSQL_CONFIG_PATH' => Path::join(__DIR__, 'mysql.cnf'),
    'SELENIUM_SERVER_PORT' => $selenium_server_port,
    'SELENIUM_SERVER_HOST' => $selenium_server_host,
    'PHP_BIN' => $php,
    'SCREEN_SHOT_URL' => $cc_screen_shot_url
];

function getTestDataDumpFilePath()
{
    return getShopTestPath() . '/Codeception/_data/dump.sql';
}

function getShopSuitePath($facts)
{
    $testSuitePath = getenv('TEST_SUITE');
    if (!$testSuitePath) {
        $testSuitePath = INSTALLATION_ROOT_PATH . '/tests';
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
    return Path::join(INSTALLATION_ROOT_PATH, 'tests', 'Codeception', 'config', 'params.php');
}
