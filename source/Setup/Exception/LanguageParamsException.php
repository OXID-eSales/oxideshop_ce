<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup\Exception;

/**
 *
 * Exception class to indicate wrong languageParams data type
 */
class LanguageParamsException extends \Exception
{
    /**
     * CommandExecutionFailedException constructor.
     *
     * @param string          $message  Name of the command which was executed.
     * @param int             $code     Exception code.
     * @param \Exception|null $previous Link to previous exception.
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
