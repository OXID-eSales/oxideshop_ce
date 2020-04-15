<?php

use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\ConfigFile;
use \Webmozart\PathUtil\Path;

define('INSTALLATION_ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..');
# Yes, adding a directory separator is stupid, but that's how the code expects it
define('OX_BASE_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);
define('OX_LOG_FILE', OX_BASE_PATH . 'log' . DIRECTORY_SEPARATOR . 'testrun.log');
# Yes, adding a directory separator is stupid, but that's how the code expects it
define('VENDOR_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);

require_once VENDOR_PATH . DIRECTORY_SEPARATOR . "autoload.php";
require_once OX_BASE_PATH . "oxfunctions.php";
require_once OX_BASE_PATH . "overridablefunctions.php";

setConfigFile();

function setConfigFile()
{
    $configFile = new ConfigFile(Path::join(OX_BASE_PATH, 'config.inc.php'));
    Registry::set(ConfigFile::class, $configFile);
}
