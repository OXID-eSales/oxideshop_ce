<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * Exception to be thrown when the database has not been configured in the configuration file config.inc.php
 */
class DatabaseNotConfiguredException extends \OxidEsales\Eshop\Core\Exception\DatabaseException
{
    /**
     * DatabaseNotConfiguredException constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous Previous exception thrown by the underlying DBAL
     */
    public function __construct($message, $code, \Exception $previous = null)
    {
        if (!$previous) {
            $previous = new \Exception();
        }
        parent::__construct($message, $code, $previous);
    }
}
