<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

require_once dirname(__FILE__) . "/bootstrap.php";

/**
 * Redirect to Setup, if shop is not configured
 */
redirectIfShopNotConfigured();

writeToLog($_SERVER['REQUEST_URI'] . '____' . $_SERVER['QUERY_STRING'] . '____' . $_SERVER['REQUEST_METHOD']);
writeToLog(serialize($_COOKIE));

//Starts the shop
OxidEsales\EshopCommunity\Core\Oxid::run();
