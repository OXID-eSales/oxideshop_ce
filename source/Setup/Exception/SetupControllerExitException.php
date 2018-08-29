<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup\Exception;

/**
 * Class SetupControllerExitException.
 *
 * Exception class to indicate an early exit from within a controller.
 */
class SetupControllerExitException extends \Exception
{
    private $templateFileName = null;

    /**
     * SetupControllerExitException constructor.
     *
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($message) {
            $this->templateFileName = $message;
        }
    }

    /**
     * Getter for template file name.
     *
     * @return null|string
     */
    public function getTemplateFileName()
    {
        return $this->templateFileName;
    }
}
