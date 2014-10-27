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
 * Console Update command
 *
 * Runs OXID Console Update manager
 */
class ConsoleUpdateCommand extends oxConsoleCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('console:update');
        $this->setDescription('Update your console application');
    }

    /**
     * {@inheritdoc}
     */
    public function help(oxIOutput $oOutput)
    {
        $oOutput->writeLn('Usage: console:update');
        $oOutput->writeLn();
        $oOutput->writeLn('This command runs an update manager of OXID Console');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(oxIOutput $oOutput)
    {
        oxRegistry::get('oxConsoleUpdateManager')->run($oOutput);

        // Executing cache:clear command
        // TODO: Refactor this place because it looks nasty
        $aCommands = $this->getConsoleApplication()->getLoadedCommands();
        if (isset($aCommands['cache:clear'])) {
            $oCommand = $aCommands['cache:clear'];
            $oCommand->setConsoleApplication($this->getConsoleApplication());
            $oCommand->setInput($this->getInput());
            $oCommand->execute($oOutput);
        }
    }
}