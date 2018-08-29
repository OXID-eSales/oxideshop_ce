<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

require_once '../bootstrap.php';

/** moduleAutoload must be unregistered, as it would trigger a database connection, which is not yet available */
$moduleAutoload = \OxidEsales\EshopCommunity\Core\Autoload\ModuleAutoload::class ;
spl_autoload_unregister([$moduleAutoload, 'autoload']);

error_reporting((E_ALL ^ E_NOTICE) | E_STRICT);

require_once 'functions.php';

$oDispatcher = new Dispatcher();
$oDispatcher->run();
