<?php
/**
 * This file is part of OXID Console.
 *
 * OXID Console is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID Console is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Console.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    OXID Professional services
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Abstract Console Command class
 *
 * All console application commands must extend this class
 */
abstract class oxConsoleCommand
{

    /**
     * @var string Command name
     */
    protected $_sName;

    /**
     * @var string Command description
     */
    protected $_sDescription;

    /**
     * @var oxIConsoleInput
     */
    protected $_oInput;

    /**
     * @var oxConsoleApplication
     */
    protected $_oConsoleApplication;

    /**
     * Constructor
     *
     * Configures console command
     *
     * @throws oxConsoleException
     */
    public function __construct()
    {
        $this->configure();

        if (!$this->getName()) {
            /** @var oxConsoleException $oEx */
            $oEx = oxNew('oxConsoleException');
            $oEx->setMessage('Command must have a name.');
            throw $oEx;
        }
    }

    /**
     * Configure current command
     *
     * Usage:
     *   $this->setName( 'my:command' )
     *   $this->setDescription( 'Executes my command' );
     */
    abstract public function configure();

    /**
     * Output help text of command
     *
     * @param oxIOutput $oOutput
     */
    abstract public function help(oxIOutput $oOutput);

    /**
     * Execute current command
     *
     * @param oxIOutput $oOutput
     */
    abstract public function execute(oxIOutput $oOutput);

    /**
     * Set current console command name
     *
     * @param string $sName
     */
    public function setName($sName)
    {
        $this->_sName = $sName;
    }

    /**
     * Get current console command name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_sName;
    }

    /**
     * Set current console command description
     *
     * @param string $sDescription
     */
    public function setDescription($sDescription)
    {
        $this->_sDescription = $sDescription;
    }

    /**
     * Get current console command description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_sDescription;
    }

    /**
     * Set console application
     *
     * @param oxConsoleApplication $oConsoleApplication
     */
    public function setConsoleApplication(oxConsoleApplication $oConsoleApplication)
    {
        $this->_oConsoleApplication = $oConsoleApplication;
    }

    /**
     * Set input instance
     *
     * @param oxIConsoleInput $oInput
     */
    public function setInput(oxIConsoleInput $oInput)
    {
        $this->_oInput = $oInput;
    }

    /**
     * Get input instance
     *
     * @return oxIConsoleInput
     */
    public function getInput()
    {
        return $this->_oInput;
    }

    /**
     * Get console application
     *
     * @return oxConsoleApplication
     */
    public function getConsoleApplication()
    {
        return $this->_oConsoleApplication;
    }
}