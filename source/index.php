<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Http\KernelFactory;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

if (filter_var(getenv('OXID_DEBUG_MODE'), FILTER_VALIDATE_BOOLEAN)) {
    Debug::enable();
}

$kernel = (new KernelFactory())->create();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
Registry::getSession()->freeze();
$kernel->terminate($request, $response);
