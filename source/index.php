<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$kernel = OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->getKernel();
if ($kernel->isDebug()) {
    Symfony\Component\ErrorHandler\Debug::enable();
}
$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
OxidEsales\Eshop\Core\Registry::getSession()->freeze();
