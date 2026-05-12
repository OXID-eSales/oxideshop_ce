<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

if (!defined('OX_IS_ADMIN')) {
    define('OX_IS_ADMIN', true);
}

if (!defined('OX_ADMIN_DIR')) {
    define('OX_ADMIN_DIR', dirname(__FILE__));
}

require_once dirname(__FILE__) . '/../bootstrap.php';

$kernel = OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->getKernel();
if ($kernel->isDebug()) {
    Symfony\Component\ErrorHandler\Debug::enable();
}
$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
