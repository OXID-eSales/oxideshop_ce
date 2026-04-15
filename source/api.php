<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Internal\Framework\Api\ExceptionHandler;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\EshopCommunity\Internal\Framework\Http\KernelFactory;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

define('INSTALLATION_ROOT_PATH', dirname(__DIR__));
define('OX_BASE_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);

require_once VENDOR_PATH . 'autoload.php';

require_once INSTALLATION_ROOT_PATH . '/source/oxfunctions.php';
require_once INSTALLATION_ROOT_PATH . '/source/overridablefunctions.php';

new DotenvLoader(INSTALLATION_ROOT_PATH)->loadEnvironmentVariables();

set_exception_handler([new ExceptionHandler(), 'handle']);

if (filter_var(getenv('OXID_DEBUG_MODE'), FILTER_VALIDATE_BOOLEAN)) {
    Debug::enable();
}

$kernel = (new KernelFactory())->create();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
