<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;

/**
 * Exception handler, deals with all high level exceptions (caught in oxShopControl)
 */
class ExceptionHandler
{
    /**
     * Log file path/name
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
     *
     * @var string
     */
    protected $_sFileName;

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
        $this->_sFileName = basename(OX_LOG_FILE);
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
     * Set the debug level
     *
     * @param int $iDebug debug level (0== no debug)
     *
     * @deprecated since v6.3 (2018-04-25); This method will be removed completely. Use an appropriate Monolog channel in the future.
     */
    public function setIDebug($iDebug)
    {
        $this->_iDebug = $iDebug;
    }

    /**
     * Set log file name. The file will always be created in the same directory as OX_LOG_FILE
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
     *
     * @param string $fileName file name
     */
    public function setLogFileName($fileName)
    {
        $fileName = basename($fileName);

        $this->_sFileName = $fileName;
    }

    /**
     * Get log file path/name
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
     *
     * @return string
     */
    public function getLogFileName()
    {
        return basename($this->_sFileName);
    }

    /**
     * Handler for uncaught exceptions. As this is the las resort no fancy business logic should be applied here.
     *
     * @param \Throwable $exception exception object
     *
     * @throws \Throwable
     **/
    public function handleUncaughtException(\Throwable $exception)
    {
        try {
            $this->writeExceptionToLog($exception);
        } catch (\Throwable $loggerException) {
            /**
             * Its not possible to get the logger from the DI container.
             * Try again to log original exception (without DI container) in order to show the root cause of a problem.
             */
            try {
                $loggerServiceFactory = new LoggerServiceFactory(new Context(Registry::getConfig()));
                $logger = $loggerServiceFactory->getLogger();
                $logger->error($this->getFormattedException($exception));
            } catch (\Throwable $throwableWithoutPossibilityToWriteToLogFile) {
                // It is not possible to log because e.g. the log file is not writable.
            }
        }

        if ($this->_iDebug || defined('OXID_PHP_UNIT') || php_sapi_name() === 'cli') {
            throw $exception;
        } else {
            $this->displayOfflinePage();
            $this->exitApplication();
        }
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
     * Write a formatted log entry to the log file.
     *
     * @param \Throwable $exception
     *
     * @deprecated since v6.3 (2018-04-25); This method will be private. Use Registry::getLogger() to log error messages in the future.
     *
     * @return bool
     */
    public function writeExceptionToLog($exception)
    {
        $logger = Registry::getLogger();
        $logger->error($exception->getMessage(), [$exception]);

        /** return statement is @deprecated since v6.3 (2018-04-19); The return value of this method will be void. */
        return true;
    }

    /**
     * Render an error message.
     * If offline.html exists its content is displayed.
     * Like this the error message is overridable within that file.
     * Do not display an error message, if this file is included during a CLI command
     *
     * @deprecated since v6.3 (2018-04-25); This method will be private. Use \oxTriggerOfflinePageDisplay() in the future.
     *
     * @return null
     */
    public function displayOfflinePage()
    {
        \oxTriggerOfflinePageDisplay();

        return;
    }

    /**
     * Print a debug message to the screen.
     *
     * @param \Throwable $exception  The exception to be treated
     * @param bool       $logWritten True, if an entry was written to the log file
     *
     * @deprecated since v6.3 (2018-04-25); This method will be removed completely. Use an appropriate Monolog channel in the future.
     *
     * @return null
     */
    protected function displayDebugMessage($exception, $logWritten = true)
    {
        $loggingErrorMessage = $logWritten ? '' : ' Could not write log file' . PHP_EOL;

        /** Just display a small note in CLI mode */
        $phpSAPIName = strtolower(php_sapi_name());
        if ('cli' === $phpSAPIName) {
            echo 'Uncaught exception. See error log for more information.' . $loggingErrorMessage;
            return;
        }
        if (method_exists($exception, 'getString')) {
            $displayMessage = $exception->getString();
        } else {
            $displayMessage = $this->getFormattedException($exception);
        }
        echo '<pre>' . $displayMessage . $loggingErrorMessage . '</pre>';

        return;
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
    public function getFormattedException($exception)
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
     * Exit the application with error status 1
     */
    protected function exitApplication()
    {
        exit(1);
    }
}
