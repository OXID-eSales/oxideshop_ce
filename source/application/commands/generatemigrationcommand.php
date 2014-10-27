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
 * Generate migration console command
 */
class GenerateMigrationCommand extends oxConsoleCommand
{

    /**
     * Configure current command
     *
     * Usage:
     *   $this->setName( 'my:command' )
     *   $this->setDescription( 'Executes my command' );
     */
    public function configure()
    {
        $this->setName('g:migration');
        $this->setDescription('Generate new migration file');
    }

    /**
     * Output help text of command
     *
     * @param oxIOutput $oOutput
     */
    public function help(oxIOutput $oOutput)
    {
        $oOutput->writeLn('Usage: g:migration <word> [<second_word>...]');
        $oOutput->writeLn();
        $oOutput->writeLn('Generates blank migration class.');
        $oOutput->writeLn('Migration name depends on words you have written.');
        $oOutput->writeLn();
        $oOutput->writeLn('If no words were passed you will be asked to input them');
    }

    /**
     * Execute current command
     *
     * @param oxIOutput $oOutput
     */
    public function execute(oxIOutput $oOutput)
    {
        $sMigrationsDir = OX_BASE_PATH . 'migration' . DIRECTORY_SEPARATOR;
        $sTemplatePath = $this->_getTemplatePath();

        $sMigrationName = $this->_parseMigrationNameFromInput();
        if (!$sMigrationName) {
            do {
                $sMigrationName = $this->_askForMigrationNameInput();
            } while (!$sMigrationName);
        }

        $sMigrationFilePath = $sMigrationsDir . oxMigrationQuery::getCurrentTimestamp() . '_'
            . strtolower($sMigrationName) . '.php';

        /** @var Smarty $oSmarty */
        $oSmarty = oxRegistry::get('oxUtilsView')->getSmarty();
        $oSmarty->assign('sMigrationName', $sMigrationName);
        $sContent = $oSmarty->fetch($sTemplatePath);

        file_put_contents($sMigrationFilePath, $sContent);
    }

    /**
     * Get template path
     *
     * This allows us to override where template file is stored
     *
     * @return string
     */
    protected function _getTemplatePath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'migration.tpl';
    }

    /**
     * Ask for migration tokens input
     *
     * @return array
     */
    protected function _askForMigrationNameInput()
    {
        $oInput = $this->getInput();
        $aTokens = explode(' ', $oInput->prompt('Enter short description'));

        return $this->_buildMigrationName($aTokens);
    }

    /**
     * Parse migration name from input arguments
     *
     * @return string
     */
    protected function _parseMigrationNameFromInput()
    {
        $oInput = $this->getInput();

        $aTokens = $oInput->getArguments();
        array_shift($aTokens); // strip out command name

        return $this->_buildMigrationName($aTokens);
    }

    /**
     * Build migration name from tokens
     *
     * @param array $aTokens
     *
     * @return string
     */
    protected function _buildMigrationName(array $aTokens)
    {
        $sMigrationName = '';

        foreach ($aTokens as $sToken) {

            if (!$sToken) {
                continue;
            }

            $sMigrationName .= ucfirst($sToken);
        }

        return $sMigrationName;
    }
}
