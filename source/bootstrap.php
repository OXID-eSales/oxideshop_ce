<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler;
use OxidEsales\EshopCommunity\Core\Autoload\BackwardsCompatibilityAutoload;
use OxidEsales\EshopCommunity\Core\Autoload\ModuleAutoload;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;

define('INSTALLATION_ROOT_PATH', dirname(__DIR__));
const OX_BASE_PATH = INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR;
const VENDOR_PATH = INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;

require_once VENDOR_PATH . 'autoload.php';

(new DotenvLoader(INSTALLATION_ROOT_PATH))->loadEnvironmentVariables();

if (!function_exists('oxTriggerOfflinePageDisplay')) {
    function oxTriggerOfflinePageDisplay(): void
    {
        if (strtolower(PHP_SAPI) !== 'cli') {
            header('HTTP/1.1 500 Internal Server Error');
            header('Connection: close');
            $offlineFile = OX_BASE_PATH . 'offline.html';
            if (is_readable($offlineFile)) {
                echo file_get_contents($offlineFile);
            }
        }
    }
}

/** For errors not caught by the application. */
register_shutdown_function(
    static function () {
        $lastError = error_get_last();
        $fatalErrors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR];
        if ($lastError && in_array($lastError['type'], $fatalErrors, true)) {
            file_put_contents(
                OX_BASE_PATH . 'log' . DIRECTORY_SEPARATOR . 'oxideshop.log',
                "Application has shut down with an UNCAUGHT ERROR: '" . print_r($lastError, true) . "'\n",
                FILE_APPEND
            );

            if (!filter_var(getenv('OXID_DEBUG_MODE'), FILTER_VALIDATE_BOOLEAN)) {
                oxTriggerOfflinePageDisplay();
            }
            if ($lastError['type'] === E_ERROR) {
                setcookie(name: 'sid', path: '/');
                setcookie(name: 'admin_sid', path: '/');
            }
        }
    }
);

spl_autoload_register([BackwardsCompatibilityAutoload::class, 'autoload']);
spl_autoload_register([ModuleAutoload::class, 'autoload']);

/** Set exception handler before including modules/functions.php, so it can be overwritten by shop operators. */
set_exception_handler(
    [
        new ExceptionHandler(filter_var(getenv('OXID_DEBUG_MODE'), FILTER_VALIDATE_BOOLEAN)),
        'handleUncaughtException'
    ]
);

require_once OX_BASE_PATH . 'oxfunctions.php';
if (is_readable(OX_BASE_PATH . 'modules/functions.php')) {
    include OX_BASE_PATH . 'modules/functions.php';
}
require_once OX_BASE_PATH . 'overridablefunctions.php';

date_default_timezone_set(getenv('OXID_DEFAULT_TIMEZONE') ?: 'Europe/Berlin');
