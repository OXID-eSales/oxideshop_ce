<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\DoctrineMigrationWrapper;

use OxidEsales\Facts\Facts;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\TestingLibrary\Services\Library\DatabaseDefaultsFileGenerator;

$facts = new Facts();

$selenium_server_port = getenv('SELENIUM_SERVER_PORT');
$selenium_server_port = ($selenium_server_port) ? $selenium_server_port : '4444';
$php = (getenv('PHPBIN')) ? getenv('PHPBIN') : 'php';

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
    'MYSQL_CONFIG_PATH' => getMysqlConfigPath(),
    'SELENIUM_SERVER_PORT' => $selenium_server_port,
    'PHP_BIN' => $php,
];

function getTestDataDumpFilePath()
{
    return getShopTestPath().'/Codeception/_data/dump.sql';
}

function getShopSuitePath($facts)
{
    $testSuitePath = getenv('TEST_SUITE');
    if (!$testSuitePath) {
        $testSuitePath = $facts->getShopRootPath().'/tests';
    }
    return $testSuitePath;
}

function getShopTestPath()
{
    $facts = new Facts();

    if ($facts->isEnterprise()) {
        $shopTestPath = $facts->getEnterpriseEditionRootPath().'/Tests';
    } else {
        $shopTestPath = getShopSuitePath($facts);
    }
    return $shopTestPath;
}

function getMysqlConfigPath()
{
    $facts = new Facts();
    $configFile = new ConfigFile($facts->getSourcePath() . '/config.inc.php');

    $generator = new DatabaseDefaultsFileGenerator($configFile);

    return $generator->generate();
}
