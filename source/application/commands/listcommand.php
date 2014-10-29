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
 * List command
 *
 * Display all available commands in console application
 */
class ListCommand extends oxConsoleCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('list');
        $this->setDescription('List of all available commands');
    }

    /**
     * {@inheritdoc}
     */
    public function help(oxIOutput $oOutput)
    {
        $this->execute($oOutput);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(oxIOutput $oOutput)
    {
        $aCommands = $this->getConsoleApplication()
            ->getLoadedCommands();

        $oOutput->writeLn('OXID Shop console');
        $oOutput->writeLn();
        $oOutput->writeLn('Available commands:');

        $iOffset = max(array_map('strlen', array_keys($aCommands))) + 2;

        foreach ($aCommands as $oCommand) {
            $sName = $oCommand->getName();
            $sDescription = $oCommand->getDescription();
            $iDescriptionOffset = $iOffset - strlen($sName);

            $oOutput->writeLn(sprintf("  %s %{$iDescriptionOffset}s # %s", $sName, ' ', $sDescription));
        }
    }
}