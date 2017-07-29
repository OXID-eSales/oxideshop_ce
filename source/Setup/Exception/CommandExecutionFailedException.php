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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
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
