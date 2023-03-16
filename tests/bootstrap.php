<?php

define('INSTALLATION_ROOT_PATH', (new \OxidEsales\Facts\Facts())->getShopRootPath());
# Yes, adding a directory separator is stupid, but that's how the code expects it
define('VENDOR_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
//require VENDOR_PATH . DIRECTORY_SEPARATOR . 'autoload.php';

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use \Symfony\Component\Filesystem\Path;

# Yes, adding a directory separator is stupid, but that's how the code expects it
define('OX_BASE_PATH', Path::join(INSTALLATION_ROOT_PATH, 'source') . DIRECTORY_SEPARATOR);
//define('OX_LOG_FILE', Path::join(OX_BASE_PATH, 'log', 'testrun.log'));
require_once Path::join(OX_BASE_PATH, 'oxfunctions.php');
require_once Path::join(OX_BASE_PATH, 'overridablefunctions.php');

// Configure Registry
$configFile = new ConfigFile(Path::join(OX_BASE_PATH, 'config.inc.php'));
Registry::set(ConfigFile::class, $configFile);
