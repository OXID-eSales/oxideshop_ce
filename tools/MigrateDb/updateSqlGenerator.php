<?php

/**
 * This script is intended to generate the SQL file for migrating oxincl based multishop data to the mapping table.
 * It takes updateSingleShop.tpl.sql template file as a input, iterates through the subshops and generates
 * the migration script.
 *
 * USAGE:
 * - manually set $NUMBER_OF_SUBSHOPS constant to required number of shops
 * - run this script:
 *      php 2-updateSqlGenerator.php
 * - your result file 3-migrate.sql is ready
 *
 * @copyright (c)2014 OXID eSales AG
 */

$NUMBER_OF_SUBSHOPS = 1;





$fOut = fopen("2-migrate.sql", "w");
$sSqlIn = file_get_contents("updateSingleShop.tpl.sql");

for($i = 1; $i <= $NUMBER_OF_SUBSHOPS; $i++)
{
    $sSql = str_replace("<shop_id>", $i, $sSqlIn);
    fputs($fOut, $sSql);
}

fclose($fOut);
