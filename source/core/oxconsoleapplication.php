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
 * Console Application is a container for collections of commands
 *
 * Loads all available commands on application start from
 *  - application/commands
 *  - [module_path]/commands
 *
 * Sample usage:
 *      $oMyInput = oxNew( 'myConsoleInput' );
 *      $oConsole = oxNew( 'oxConsoleApplication' );
 *      $oConsole->add( oxNew( 'myCustomCommand' ) );
 *      $oConsole->run( $oMyInput );
 */
class oxConsoleApplication
{

    /**
     * OXID Console application version
     */
    const VERSION = 'v1.1.3';

    /**
     * @var oxConsoleCommand[] Available commands in console
     */
    protected $_aCommands = array();

    /**
     * @var oxConsoleCommand
     */
    protected $_oDefaultCommand;

    /**
     * Console application constructor
     *
     * Loads command files from core and modules commands directories
     *
     * @param string $sDefaultCommandName Default command name
     */
    public function __construct($sDefaultCommandName = 'list')
    {
        $this->_loadCoreCommands();
        $this->_loadModulesCommands();

        if (isset($this->_aCommands[$sDefaultCommandName])) {
            $this->setDefaultCommand($this->_aCommands[$sDefaultCommandName]);
        }

        // Sorting commands in ascending order
        ksort($this->_aCommands);
    }

    /**
     * Runs Console application
     *
     * When no oxIConsoleInput arguments are passed displaying available commands list
     * oxIConsoleInput::getFirstArgument is a command name of which to execute
     *
     * @param oxIConsoleInput $oInput
     * @param oxIOutput $oOutput
     */
    public function run(oxIConsoleInput $oInput = null, oxIOutput $oOutput = null)
    {
        if ($oInput === null) {
            /** @var oxArgvInput $oInput */
            $oInput = oxNew('oxArgvInput');
        }

        if ($oOutput === null) {
            /** @var oxConsoleOutput $oOutput */
            $oOutput = oxNew('oxConsoleOutput');
        }

        $sCommandName = $oInput->getFirstArgument();
        $oCommand = null;

        if (!$sCommandName) {
            if ($oInput->hasOption(array('v', 'version'))) {
                $oOutput->writeLn('OXID Console ' . static::VERSION);
                return;
            }

            $oCommand = $this->getDefaultCommand();
        } elseif (array_key_exists($sCommandName, $this->_aCommands)) {
            $oCommand = $this->_aCommands[$sCommandName];
        }

        if ($oCommand === null) {
            $oOutput->writeLn('Could not find command: ' . $sCommandName);

            return;
        }

        $this->_setupCommand($oCommand, $oInput);

        $oInput->hasOption(array('help', 'h'))
            ? $oCommand->help($oOutput)
            : $oCommand->execute($oOutput);

        $oOutput->writeLn();
    }

    /**
     * Set up given command
     *
     * @param oxConsoleCommand $oCommand
     * @param oxIConsoleInput $oInput
     */
    protected function _setupCommand(oxConsoleCommand $oCommand, oxIConsoleInput $oInput)
    {
        $oCommand->setInput($oInput);
        $oCommand->setConsoleApplication($this);
    }

    /**
     * Get all loaded commands
     *
     * @return oxConsoleCommand[]
     */
    public function getLoadedCommands()
    {
        return $this->_aCommands;
    }

    /**
     * Set default command
     *
     * @param oxConsoleCommand $oCommand
     */
    public function setDefaultCommand(oxConsoleCommand $oCommand)
    {
        $this->_oDefaultCommand = $oCommand;
    }

    /**
     * Get default command
     *
     * @return oxConsoleCommand
     */
    public function getDefaultCommand()
    {
        return $this->_oDefaultCommand;
    }

    /**
     * Add Command into Console Application
     *
     * @param oxConsoleCommand $oCommand
     *
     * @throws oxConsoleException If was already defined
     */
    public function add(oxConsoleCommand $oCommand)
    {
        $sCommandName = $oCommand->getName();
        if (array_key_exists($sCommandName, $this->_aCommands)) {
            /** @var oxConsoleException $oEx */
            $oEx = oxNew('oxConsoleException');
            $oEx->setMessage($sCommandName . ' has more then one definition');
            throw $oEx;
        }

        $this->_aCommands[$sCommandName] = $oCommand;
    }

    /**
     * Remove command from Console application
     *
     * @param string $sCommandName
     */
    public function remove($sCommandName)
    {
        unset($this->_aCommands[$sCommandName]);
    }

    /**
     * Load core console application commands
     *
     * Loads all command files from application/commands directory
     */
    protected function _loadCoreCommands()
    {
        $sDirectory = OX_BASE_PATH . 'application' . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR;
        $this->_loadCommands($sDirectory);
    }

    /**
     * Loads command files from modules path
     *
     * Loads all command files from [module_path]/commands
     */
    protected function _loadModulesCommands()
    {
        $oConfig = oxRegistry::getConfig();
        $sModulesDir = $oConfig->getModulesDir();
        $aModulePaths = $oConfig->getConfigParam('aModulePaths');

        if (!is_array($aModulePaths)) {
            return;
        }

        foreach ($aModulePaths as $sModulePath) {
            $sCommandDir = $sModulesDir . $sModulePath . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR;
            $this->_loadCommands($sCommandDir);
        }
    }

    /**
     * Load all commands from given directory
     *
     * Command file names are defined following "*command.php" format
     * Class name should be the same as file name excluding ".php" and case is ignored
     *
     * Has directory check inside so you do not have to worry about passing not existing
     * directories
     *
     * @param string $sDirectory
     */
    protected function _loadCommands($sDirectory)
    {
        if (!is_dir($sDirectory)) {
            return;
        }

        $oDirectory = new RecursiveDirectoryIterator($sDirectory);
        $oFlattened = new RecursiveIteratorIterator($oDirectory);

        $aFiles = new RegexIterator($oFlattened, '/.*command\.php$/');
        foreach ($aFiles as $sFilePath) {
            require_once $sFilePath;

            $sClassName = substr(basename($sFilePath), 0, -4);

            /** @var oxConsoleCommand $oCommand */
            $oCommand = oxNew($sClassName);
            $this->add($oCommand);
        }
    }
}
