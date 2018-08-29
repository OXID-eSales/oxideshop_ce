<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup\Exception;

/**
 * Class TemplateNotFoundException.
 *
 * Exception class to indicate absence of template
 */
class TemplateNotFoundException extends \Exception
{
    /**
     * TemplateNotFoundException constructor.
     *
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        $message = sprintf("Template named '%s' was not found.", $message);

        parent::__construct($message, $code, $previous);
    }
}
