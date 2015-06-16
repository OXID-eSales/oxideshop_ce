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
namespace OxidEsales\Eshop\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use OxidEsales\Eshop\Core\Contract\LoggerFactoryInterface;

/**
 * Class LoggerFactory
 * @package OxidEsales\Eshop\Core
 */
class MonologFactory implements LoggerFactoryInterface
{
    /**
     * creates a logger object
     * this method should be called only once during a request
     * no internal caching is done because logger object can be stored in caller or registry
     *
     * this method is called during bootstrap be careful to not create cycle dependencies
     * @param $name string name of the Logger default is 'root' in the moment of writing 
     * the only use case is to generate the 'root' logger which is the default if $name is not given.
     * There is not yet any support for channels
     * in this default implementation giving an other name then root only guarantees to get a mono logger with that name, but
     * the configuration is not defined, it might be the same like on the root logger or a logger with no configuration at all.
     * Plan is to make this somehow configurable by configuration files.
     * @return Logger
     *      
     */
    public function getLogger($name = 'root'){
        // create root logger
        $log = new Logger($name);

        $this->basicLoggerConfiguration($log);
        $this->standardLoggerConfiguration($log);

        /*
         * every enhancing of the root logger that would create cycle dependencies during bootstrap
         * should be done in a separate class called after basic bootstrapping
         */

        return $log;
    }

    /**
     * method to do basic logger configuration.
     * This should not be changed in subclasses unless you have a good reason to do so
     * because it provides the basic contract that errors are logged to STDERR and PSR3 interpolation is supported
     * @param $log Logger Monolog implementation that should be configured
     */
    public function basicLoggerConfiguration($log)
    {
        //add monolog psr3 interpolation support (using context)
        //because its part of psr3 standard
        $log->pushProcessor(new PsrLogMessageProcessor());

        //log errors to stderr which normally will be loged to file (error.log) by the server
        //because this is the standard place for errors
        //also fatal errors not handled by oxid will be there
        $log->pushHandler(new StreamHandler('php://stderr', Logger::ERROR));
    }

    /**
     *
     * @param $log Logger Monolog implementation that should be configured
     */
    public function standardLoggerConfiguration($log){
        $config = Registry::get("oxConfigFile");
        $debug = (bool) $config->getVar("iDebug");

        $level = $debug ? Logger::DEBUG : Logger::WARNING;

        //Todo use somehow Config->getLogsDir()
        $dir = $config->getVar('sShopDir') . 'log/';
        $log->pushHandler(new StreamHandler($dir . 'oxid.log', $level));

        //legacy exception log because that's the well known place for oxid developers to search for errors
        $log->pushHandler(new StreamHandler($dir . 'EXCEPTION_LOG.txt', Logger::ERROR));
    }
}