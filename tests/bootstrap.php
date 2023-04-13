<?php

define('INSTALLATION_ROOT_PATH', (new \OxidEsales\Facts\Facts())->getShopRootPath());
# Yes, adding a directory separator is stupid, but that's how the code expects it
define('VENDOR_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
require VENDOR_PATH . DIRECTORY_SEPARATOR . 'autoload.php';

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use \Symfony\Component\Filesystem\Path;

# Yes, adding a directory separator is stupid, but that's how the code expects it
define('OX_BASE_PATH', Path::join(INSTALLATION_ROOT_PATH, 'source') . DIRECTORY_SEPARATOR);

/**
 * Where CORE_AUTOLOADER_PATH points depends on how OXID eShop has been installed. If it is installed as part of a
 * compilation, the directory 'Core', where the auto load classes are located, does not reside inside OX_BASE_PATH,
 * but inside VENDOR_PATH.
 */
if (!is_dir(OX_BASE_PATH . 'Core')) {
    define('CORE_AUTOLOADER_PATH', (new \OxidEsales\Facts\Facts())->getCommunityEditionSourcePath() .
        DIRECTORY_SEPARATOR .
        'Core' . DIRECTORY_SEPARATOR .
        'Autoload' . DIRECTORY_SEPARATOR);
} else {
    define('CORE_AUTOLOADER_PATH', OX_BASE_PATH . 'Core' . DIRECTORY_SEPARATOR . 'Autoload' . DIRECTORY_SEPARATOR);
}

require_once CORE_AUTOLOADER_PATH . 'BackwardsCompatibilityAutoload.php';
spl_autoload_register([OxidEsales\EshopCommunity\Core\Autoload\BackwardsCompatibilityAutoload::class, 'autoload']);

require_once CORE_AUTOLOADER_PATH . 'ModuleAutoload.php';
spl_autoload_register([\OxidEsales\EshopCommunity\Core\Autoload\ModuleAutoload::class, 'autoload']);


//define('OX_LOG_FILE', Path::join(OX_BASE_PATH, 'log', 'testrun.log'));
require_once Path::join(OX_BASE_PATH, 'oxfunctions.php');
require_once Path::join(OX_BASE_PATH, 'overridablefunctions.php');


// Configure Registry
$configFile = new ConfigFile(Path::join(OX_BASE_PATH, 'config.inc.php'));
Registry::set(ConfigFile::class, $configFile);
