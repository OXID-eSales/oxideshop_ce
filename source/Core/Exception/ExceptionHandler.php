<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

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
        /**
         *  If $fileName !== basename($fileName) throw exception
         */
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
     * @param $exception throwable
     *
     * @return void
     **/
    public function handleUncaughtException($exception)
    {
        /**
         * Report the exception
         */
        $logWritten = (bool) $this->writeExceptionToLog($exception);

        /**
         * Render an error message.
         */
        if ($this->_iDebug) {
            $this->displayDebugMessage($exception, $logWritten);
        } else {
            $this->displayOfflinePage();
        }

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
     * Write a formatted log entry to the log file.
     *
     * @param throwable $exception
     *
     * @return int|false The function returns the number of bytes that were written to the file, or false on failure.
     */
    public function writeExceptionToLog($exception)
    {
        /** self::_sFileName is @deprecated since v6.0 (2017-03-30); Logging mechanism will change in the future. */
        $logFile = dirname(OX_LOG_FILE) . DIRECTORY_SEPARATOR . $this->_sFileName;
        $logMessage = $this->getFormattedException($exception);

        return file_put_contents($logFile, $logMessage, FILE_APPEND) !== false ? true : false;
    }

    /**
     * Render an error message.
     * If offline.html exists its content is displayed.
     * Like this the error message is overridable within that file.
     * Do not display an error message, if this file is included during a CLI command
     *
     * @return null
     */
    public function displayOfflinePage()
    {
        /** Just display a small note in CLI mode */
        $phpSapiName = strtolower(php_sapi_name());
        if ('cli' === $phpSapiName) {
            echo 'Uncaught exception. See ' . $this->getLogFileName() . PHP_EOL;
            return;
        }

        \oxTriggerOfflinePageDisplay();

        return;
    }

    /**
     * Print a debug message to the screen.
     *
     * @param throwable $exception
     * @param bool       $logWritten True, if an entry was written to the log file
     *
     * @return null
     */
    protected function displayDebugMessage($exception, $logWritten)
    {
        $loggingErrorMessage = $logWritten ? '' : 'Could not write log file' . PHP_EOL;

        /** Just display a small note in CLI mode */
        $phpSapiName = strtolower(php_sapi_name());
        if ('cli' === $phpSapiName) {
            echo 'Uncaught exception. See ' . $this->getLogFileName() . PHP_EOL . $loggingErrorMessage;
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
     * @param throwable $exception
     *
     * @return string
     */
    protected function getFormattedException($exception)
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
