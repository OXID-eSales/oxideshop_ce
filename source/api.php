<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Internal\Framework\Api\Api;
use OxidEsales\EshopCommunity\Internal\Framework\Api\ExceptionHandler;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use Symfony\Component\ErrorHandler\Debug;

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

new Api()->run();
