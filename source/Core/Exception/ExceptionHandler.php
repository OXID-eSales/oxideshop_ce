<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

use OxidEsales\Eshop\Core\Registry;

/**
 * Exception handler, deals with all high level exceptions (caught in oxShopControl)
 */
class ExceptionHandler
{
    /**
     * Shop debug
     *
     * @deprecated since v6.3 (2018-04-25); This functionality will be removed completely. Use an appropriate Monolog channel in the future.
     *
     * @var integer
     */
    protected $_iDebug = 0;

    /**
     * Class constructor
     *
     * @param integer $iDebug debug level
     */
    public function __construct($iDebug = 0)
    {
        $this->_iDebug = (int) $iDebug;
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $sMethod Methods name
     * @param array  $aArgs   Argument array
     *
     * @throws SystemComponentException Throws an exception if the called method does not exist or is not accessible in current class
     *
     * @return string
     */
    public function __call($sMethod, $aArgs)
    {
        if (defined('OXID_PHP_UNIT')) {
            if (substr($sMethod, 0, 4) == "UNIT") {
                $sMethod = str_replace("UNIT", "_", $sMethod);
            }
            if (method_exists($this, $sMethod)) {
                return call_user_func_array([& $this, $sMethod], $aArgs);
            }
        }

        throw new \OxidEsales\Eshop\Core\Exception\SystemComponentException(
            "Function '$sMethod' does not exist or is not accessible! (" . __CLASS__ . ")" . PHP_EOL
        );
    }

    /**
     * Handler for uncaught exceptions. As this is the las resort no fancy business logic should be applied here.
     *
     * @param \Throwable $exception exception object
     *
     * @return void
     **/
    public function handleUncaughtException(\Throwable $exception)
    {
        Registry::getLogger()->error(
            $exception->getMessage(),
            [$exception]
        );

        $this->renderErrorMessage($exception);

        /**
         * Do not exit the application in UNIT tests
         */
        if (defined('OXID_PHP_UNIT')) {
            return;
        }
        $this->exitApplication();
    }

    /**
     * Report the exception and in case that iDebug is not set, redirect to maintenance page.
     * Special methods are used here as the normal exception handling routines always need a database connection and
     * this would create a loop.
     *
     * @param \OxidEsales\Eshop\Core\Exception\DatabaseException $exception Exception to handle
     */
    public function handleDatabaseException(\OxidEsales\Eshop\Core\Exception\DatabaseException $exception)
    {
        $this->handleUncaughtException($exception);
    }


    /**
     * Exit the application with error status 1
     */
    protected function exitApplication()
    {
        exit(1);
    }

    /**
     * Print a debug message to the screen.
     *
     * @param \Throwable $exception The exception to be treated
     *
     * @deprecated since v6.3 (2018-04-25); This method will be removed completely. Use an appropriate Monolog channel in the future.
     */
    private function displayDebugMessage(\Throwable $exception)
    {
        if (method_exists($exception, 'getString')) {
            $displayMessage = $exception->getString();
        } else {
            $displayMessage = $this->getFormattedException($exception);
        }

        echo '<pre>' . $displayMessage . '</pre>';
    }

    /**
     * Return a formatted exception to be written to the log file.
     *
     * @param  \Throwable $exception
     *
     * @deprecated since v6.3 (2018-04-25); This method will be removed completely. Use an appropriate Monolog channel in the future.
     *
     * @return string
     */
    private function getFormattedException(\Throwable $exception)
    {
        $time = microtime(true);
        $micro = sprintf("%06d", ($time - floor($time)) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $micro, $time));
        $timestamp = $date->format('d M H:i:s.u Y');

        $class = get_class($exception);

        /** report the error */
        $trace = $exception->getTraceAsString();
        $lines = explode(PHP_EOL, $trace);
        $logMessage = "[$timestamp] [exception] [type {$class}] [code {$exception->getCode()}] [file {$exception->getFile()}] [line {$exception->getLine()}] [message {$exception->getMessage()}]" . PHP_EOL;
        foreach ($lines as $line) {
            $logMessage .= "[$timestamp] [exception] [stacktrace] " . $line . PHP_EOL;
        }

        return $logMessage;
    }

    /**
     * @param \Throwable $exception
     */
    private function renderErrorMessage(\Throwable $exception)
    {
        if ($this->_iDebug) {
            $this->displayDebugMessage($exception);
        } else {
            \oxTriggerOfflinePageDisplay();
        }
    }
}
