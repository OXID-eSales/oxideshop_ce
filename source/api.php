<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Api\ExceptionHandler;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/bootstrap.php';

set_exception_handler([new ExceptionHandler(), 'handle']);

$kernel = ContainerFactory::getInstance()->getKernel();
if ($kernel->isDebug()) {
    Debug::enable();
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
