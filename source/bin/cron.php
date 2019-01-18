<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


require_once dirname(__FILE__) . "/../bootstrap.php";

// initializes singleton config class
$myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

// executing maintenance tasks..
oxNew(\OxidEsales\Eshop\Application\Model\Maintenance::class)->execute();

// closing page, writing cache and so on..
$myConfig->pageClose();
