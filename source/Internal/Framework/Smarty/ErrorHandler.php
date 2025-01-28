<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty;

/**
 * Class SmartyEngine
 * @internal
 */
class ErrorHandler
{

    private $previousErrorHandler = null;

    /**
     * @var bool $hideErrors set to true if you want to see smarty errors
     */
    private bool $hideErrors;

    public function __construct($hideErrors = false)
    {
        $this->hideErrors = $hideErrors;
    }

    /**
     * Enable error handler to intercept errors
     */
    public function activate()
    {
        /*
            Error muting is done because some people implemented custom error_handlers using
            https://php.net/set_error_handler and for some reason did not understand the following paragraph:

            It is important to remember that the standard PHP error handler is completely bypassed for the
            error types specified by error_types unless the callback function returns FALSE.
            error_reporting() settings will have no effect and your error handler will be called regardless -
            however you are still able to read the current value of error_reporting and act appropriately.
            Of particular note is that this value will be 0 if the statement that caused the error was
            prepended by the @ error-control operator.
        */
        if ($this->hideErrors) {
            $this->previousErrorHandler = set_error_handler([$this, 'handleError']);
        }
    }

    /**
     * Disable error handler
     */
    public function deactivate()
    {
        if ($this->hideErrors) {
            restore_error_handler();
            $this->previousErrorHandler = null;
        }
    }

    /**
     * Error Handler to mute expected messages
     *
     * @link https://php.net/set_error_handler
     *
     * @param integer $errno Error level
     * @param         $errstr
     * @param         $errfile
     * @param         $errline
     * @param         $errcontext
     *
     * @return bool
     */
    public function handleError($errno, $errstr, $errfile, $errline, $errcontext = [])
    {
        if (preg_match('/^(Undefined property)/', $errstr)) {
            return; // suppresses this error
        }

        if (preg_match('/^(Undefined index|Undefined array key|Trying to access array offset on)/', $errstr)) {
            return; // suppresses this error
        }

        if (preg_match('/^Attempt to read property ".+?" on/', $errstr)) {
            return; // suppresses this error
        }

        // pass all other errors through to the previous error handler or to the default PHP error handler
        return $this->previousErrorHandler ?
            call_user_func($this->previousErrorHandler, $errno, $errstr, $errfile, $errline, $errcontext) : false;
    }
}
