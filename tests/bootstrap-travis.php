<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
ini_set('display_errors', true);


// Generic utility method file including autoloading definition
require_once getShopBasePath() . "/core/oxfunctions.php";

$shopDir = dirname(__DIR__) . "/source/";

$configReplace = array(
    '<dbHost_ce>'=>'localhost',
    '<dbName_ce>'=>'oxid_test',
    '<dbUser_ce>'=>'travis',
    '<dbPwd_ce>'=>'',
    '<sShopURL_ce>'=>'localhost:8080',
    '<sShopDir_ce>'=>$shopDir,
    '<sCompileDir_ce>'=>'/tmp/',
    '<iUtfMode>'=>'1',

);

$configFile = $shopDir . "config.inc.php";

$configContent = file_get_contents( $configFile );
$configContent = str_replace(
  array_keys($configReplace),
  array_values($configReplace),
  $configContent
);

file_put_contents($configFile, $configContent);
$oConfigFile = new OxConfigFile( $configFile );
oxRegistry::set("oxConfigFile", $oConfigFile);


function getShopBasePath()
{
   return dirname(__DIR__) . "/source/";
}


$oxDb = new oxDb();
$oxDb->setConfig( $oConfigFile );
$oLegacyDb = $oxDb->getDb();

$queryEndRegExp = "#;(\n|\r\n)#";
$testDataFileNames = array(
    getShopBasePath() . '/setup/sql/database.sql',
    __DIR__ . '/testsql/testdata.sql'
);
foreach ($testDataFileNames as $filePath) {
    $queryList = preg_split($queryEndRegExp, file_get_contents($filePath));

    echo "install database by $filePath\n\n";
    foreach ($queryList as $query) {
        if ($query == '' || preg_match('#^(\s+|\r\n)$#', $query)) {
            continue;
        }

        $oLegacyDb->execute($query);
    }
}

unset($oxDb, $oLegacyDb, $queryList, $query);

require_once 'bootstrap.php';
