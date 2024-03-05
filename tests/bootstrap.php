<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Autoload\BackwardsCompatibilityAutoload;
use OxidEsales\EshopCommunity\Core\Autoload\ModuleAutoload;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use Symfony\Component\Filesystem\Path;

define('INSTALLATION_ROOT_PATH', (new ProjectRootLocator())->getProjectRoot());
const VENDOR_PATH = INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
define('OX_BASE_PATH', Path::join(INSTALLATION_ROOT_PATH, 'source') . DIRECTORY_SEPARATOR);

require VENDOR_PATH . DIRECTORY_SEPARATOR . 'autoload.php';
spl_autoload_register([BackwardsCompatibilityAutoload::class, 'autoload']);
spl_autoload_register([ModuleAutoload::class, 'autoload']);

Registry::set(ConfigFile::class, new ConfigFile(Path::join(OX_BASE_PATH, 'config.inc.php')));

require_once Path::join(OX_BASE_PATH, 'oxfunctions.php');
require_once Path::join(OX_BASE_PATH, 'overridablefunctions.php');

(new DotenvLoader(INSTALLATION_ROOT_PATH))->loadEnvironmentVariables();
