<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

require_once '../bootstrap.php';

error_reporting((E_ALL ^ E_NOTICE) | E_STRICT);

require_once 'functions.php';

$oDispatcher = new Dispatcher();
$oDispatcher->run();
