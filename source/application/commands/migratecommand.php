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
 * Migrate command
 *
 * Runs migration handler with input timestamp. If no timestamp were passed
 * runs with current timestamp instead
 */
class MigrateCommand extends oxConsoleCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('migrate');
        $this->setDescription('Run migration scripts');
    }

    /**
     * {@inheritdoc}
     */
    public function help(oxIOutput $oOutput)
    {
        $oOutput->writeLn('Usage: migrate [<timestamp>]');
        $oOutput->writeLn();
        $oOutput->writeLn('This command runs migration scripts for given timestamp');
        $oOutput->writeLn('If no timestamp is passed than it assumes timestamp is current time');
        $oOutput->writeLn();
        $oOutput->writeLn('Available options:');
        $oOutput->writeLn('  -n, --no-debug    No debug output');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(oxIOutput $oOutput)
    {
        try {
            $sTimestamp = $this->_parseTimestamp();
        } catch (oxConsoleException $oEx) {
            $oOutput->writeLn($oEx->getMessage());
            return;
        }

        $oOutput->writeLn('Running migration scripts');

        $oInput = $this->getInput();
        $oDebugOutput = $oInput->hasOption(array('n', 'no-debug'))
            ? oxNew('oxNullOutput')
            : $oOutput;

        /** @var oxMigrationHandler $oMigrationHandler */
        $oMigrationHandler = oxRegistry::get('oxMigrationHandler');
        $oMigrationHandler->run($sTimestamp, $oDebugOutput);

        $oOutput->writeLn('Migration finished successfully');
    }

    /**
     * Parse timestamp from user input
     *
     * @return string
     *
     * @throws oxConsoleException
     */
    protected function _parseTimestamp()
    {
        $oInput = $this->getInput();

        if ($sTimestamp = $oInput->getArgument(1)) {

            if (!oxMigrationQuery::isValidTimestamp($sTimestamp)) {

                if ($sTime = strtotime($sTimestamp)) {
                    $sTimestamp = date('YmdHis', $sTime);
                } else {
                    /** @var oxConsoleException $oEx */
                    $oEx = oxNew('oxConsoleException');
                    $oEx->setMessage('Invalid timestamp format, use YYYYMMDDhhmmss format');
                    throw $oEx;
                }
            }

            return $sTimestamp;
        }

        return oxMigrationQuery::getCurrentTimestamp();
    }
}
