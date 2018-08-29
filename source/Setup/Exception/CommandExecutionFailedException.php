<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup\Exception;

/**
 * Class CommandExecutionFailedException.
 *
 * Exception class to indicate absence of template
 */
class CommandExecutionFailedException extends \Exception
{
    private $command = null;

    private $returnCode = 0;

    private $commandOutput = null;

    /**
     * CommandExecutionFailedException constructor.
     *
     * @param string          $message  Name of the command which was executed.
     * @param int             $code     Exception code.
     * @param \Exception|null $previous Link to previous exception.
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        $this->command = $message;

        $message = sprintf("There was an error while executing '%s'.", $message);
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the command which was used for execution.
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Sets value for the return code.
     *
     * @param int $returnCode
     */
    public function setReturnCode($returnCode)
    {
        $this->returnCode = $returnCode;
    }

    /**
     * Returns value of return code.
     *
     * @return int
     */
    public function getReturnCode()
    {
        return $this->returnCode;
    }

    /**
     * Sets value for command output which was shown after the execution of command.
     *
     * @param array $outputLines
     */
    public function setCommandOutput($outputLines)
    {
        $this->commandOutput = $outputLines;
    }

    /**
     * Returns the value of command output which was shown after the execution of command.
     *
     * @return string
     */
    public function getCommandOutput()
    {
        return $this->commandOutput ? implode("\n", $this->commandOutput) : null;
    }
}
