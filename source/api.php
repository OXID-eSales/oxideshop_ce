<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\EshopCommunity\Core\Autoload\BackwardsCompatibilityAutoload;
use OxidEsales\EshopCommunity\Core\Autoload\ModuleAutoload;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Api\Api;
use OxidEsales\EshopCommunity\Internal\Framework\Api\ExceptionHandler;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\Facts\Facts;
use Symfony\Component\ErrorHandler\Debug;

define('INSTALLATION_ROOT_PATH', dirname(__DIR__));
define('OX_BASE_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);

require_once VENDOR_PATH . 'autoload.php';

/*
 * Register the backwards compatibility autoloader.
 * This autoloader will load classes for reasons of backwards compatibility like 'oxArticle'.
 */
require_once (new Facts())->getCommunityEditionSourcePath() . '/Core/Autoload/BackwardsCompatibilityAutoload.php';
spl_autoload_register([BackwardsCompatibilityAutoload::class, 'autoload']);

spl_autoload_register([ModuleAutoload::class, 'autoload']);

require_once INSTALLATION_ROOT_PATH . '/source/oxfunctions.php';
require_once INSTALLATION_ROOT_PATH . '/source/overridablefunctions.php';

(new DotenvLoader(INSTALLATION_ROOT_PATH))->loadEnvironmentVariables();

Registry::set(ConfigFile::class, new ConfigFile(OX_BASE_PATH . 'config.inc.php'));

set_exception_handler([new ExceptionHandler(), 'handle']);

if (filter_var(getenv('OXID_DEBUG_MODE'), FILTER_VALIDATE_BOOLEAN)) {
    Debug::enable();
}

(new Api())->run();
