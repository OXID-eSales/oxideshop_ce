<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('display_errors', 0);

define('INSTALLATION_ROOT_PATH', dirname(__DIR__));
define('OX_BASE_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);
define('OX_LOG_FILE', OX_BASE_PATH . 'log' . DIRECTORY_SEPARATOR . 'EXCEPTION_LOG.txt');
define('OX_OFFLINE_FILE', OX_BASE_PATH . 'offline.html');
define('VENDOR_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);

/**
 * Where CORE_AUTOLOADER_PATH points depends on how OXID eShop has been installed. If it is installed as part of a
 * compilation, the directory 'Core', where the auto load classes are located, does not reside inside OX_BASE_PATH,
 * but inside VENDOR_PATH.
 */
if (!is_dir(OX_BASE_PATH . 'Core')) {
    define('CORE_AUTOLOADER_PATH', VENDOR_PATH .
                                   'oxid-esales' . DIRECTORY_SEPARATOR .
                                   'oxideshop-ce' . DIRECTORY_SEPARATOR .
                                   'source' . DIRECTORY_SEPARATOR .
                                   'Core' . DIRECTORY_SEPARATOR .
                                   'Autoload' . DIRECTORY_SEPARATOR);
} else {
    define('CORE_AUTOLOADER_PATH', OX_BASE_PATH . 'Core' . DIRECTORY_SEPARATOR . 'Autoload' . DIRECTORY_SEPARATOR);
}

/**
 * Provide a handler for catchable fatal errors, like failed requirement of files.
 * No information about paths or file names must be disclosed to the frontend,
 * as this would be a security problem on productive systems.
 * This error handler is just a last resort for exceptions, which are not caught by the application.
 *
 * As this is the last resort no further errors must happen.
 */
register_shutdown_function(function () {
    $handledErrorTypes = [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR, E_USER_ERROR, E_USER_DEPRECATED];

    $error = error_get_last();
    if (in_array($error['type'], $handledErrorTypes)) {
        $errorType = array_flip(array_slice(get_defined_constants(true)['Core'], 0, 16, true))[$error['type']];

        $time = microtime(true);
        $micro = sprintf("%06d", ($time - floor($time)) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $micro, $time));
        $timestamp = $date->format('D M H:i:s.u Y');

        /** report the error */
        $logMessage = "[$timestamp] [uncaught error] [type $errorType] [file {$error['file']}] [line {$error['line']}] [code ] [message {$error['message']}]". PHP_EOL;
        file_put_contents(OX_LOG_FILE, $logMessage, FILE_APPEND);

        // Do not display an error message, if this file is included during a CLI command
        if ('cli' !== strtolower(php_sapi_name())) {
            /**
             * Render an error message.
             * If offline.html exists its content is displayed.
             * Like this the error message is overridable within that file.
             */
            $displayMessage = '';
            if (file_exists(OX_OFFLINE_FILE) && is_readable(OX_OFFLINE_FILE)) {
                $displayMessage = file_get_contents(OX_OFFLINE_FILE);
            };

            header("HTTP/1.1 500 Internal Server Error");
            header("Connection: close");
            echo $displayMessage;
        }
    }
});

/**
 * First of all ensure, that the shop config file is available.
 */
if (!is_readable(OX_BASE_PATH . "config.inc.php")) {
    $message = sprintf(
        "Config file '%s' could not be found! Please use '%s.dist' to make a copy.",
        OX_BASE_PATH . "config.inc.php",
        OX_BASE_PATH . "config.inc.php"
    );
    trigger_error($message, E_USER_ERROR);
}

/**
 * Register basic the autoloaders. In this phase we still do not want to use other shop classes to make autoloading
 * as decoupled as possible.
 */

/*
 * Require and register composer autoloader.
 * This autoloader will load classes in the real existing namespace like '\OxidEsales\EshopCommunity\Core\UtilsObject'
 * It will always come first, even if you move it after the other autoloaders as it registers itself with prepend = true
 */
require_once VENDOR_PATH . 'autoload.php';

/*
 * Require and register the alias autoloader.
 * This autoloader will load classes in the virtual namespace like '\OxidEsales\Eshop\Core\UtilsObject' or
 * for reasons of backwards compatibility classes like 'oxArticle'.
 *
 * Past this point you should use only create instances of classes from the virtual namespace
 */
require_once CORE_AUTOLOADER_PATH . 'AliasAutoload.php';

/**
 * Register the module autoload.
 * It will load classes classes defined in the metadata key 'files'
 * When this autoloader is called a database connection will be triggered
 */
require_once CORE_AUTOLOADER_PATH . 'ModuleAutoload.php';

/**
 * Store the shop configuration in the Registry prior including the custom bootstrap functionality.
 * Like this the shop configuration is available there.
 */
$configFile = new \OxidEsales\Eshop\Core\ConfigFile(OX_BASE_PATH . "config.inc.php");
\OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFile);
unset($configFile);

/**
 * Set exception handler before including modules/functions.php so it can be overwritten easliy by shop operators.
 */
$debugMode = (bool) \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar('iDebug');
set_exception_handler(
    [
        new \OxidEsales\Eshop\Core\Exception\ExceptionHandler($debugMode),
        'handleUncaughtException'
    ]
);
unset($debugMode);

/**
 * Generic utility method file.
 * The global object factory function oxNew is defined here.
 */
require_once OX_BASE_PATH . 'oxfunctions.php';

/**
 * Custom bootstrap functionality.
 */
if (is_readable(OX_BASE_PATH . 'modules/functions.php')) {
    include OX_BASE_PATH . 'modules/functions.php';
}

/**
 * The functions defined conditionally in this file may have been overwritten in 'modules/functions.php',
 * so their functionality may have changed completely.
 */
require_once OX_BASE_PATH . 'overridablefunctions.php';

//sets default PHP ini params
ini_set('session.name', 'sid');
ini_set('session.use_cookies', 0);
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', '');
