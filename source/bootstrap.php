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
use Symfony\Component\ErrorHandler\ErrorHandler;

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

spl_autoload_register([BackwardsCompatibilityAutoload::class, 'autoload']);
spl_autoload_register([ModuleAutoload::class, 'autoload']);

$debugMode = filter_var(getenv('OXID_DEBUG_MODE'), FILTER_VALIDATE_BOOLEAN);
$errorHandler = ErrorHandler::register(new ErrorHandler(debug: $debugMode));
$errorHandler->throwAt(0, true);
$errorHandler->setExceptionHandler([new ExceptionHandler($debugMode), 'handleUncaughtException']);

require_once OX_BASE_PATH . 'oxfunctions.php';
require_once OX_BASE_PATH . 'overridablefunctions.php';

ini_set('session.name', 'sid');
ini_set('session.use_cookies', 0);
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', '');

date_default_timezone_set(getenv('OXID_DEFAULT_TIMEZONE') ?: 'Europe/Berlin');
