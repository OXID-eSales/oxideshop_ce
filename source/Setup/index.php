<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

require_once '../bootstrap.php';
require_once 'functions.php';

$oDispatcher = new Dispatcher();
$oDispatcher->run();
