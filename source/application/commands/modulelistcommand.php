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
 * Module summary command
 *
 * Prints out module list table
 */
class ModuleListCommand extends oxConsoleCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('module:list');
        $this->setDescription('Outputs module list table');
    }

    /**
     * {@inheritdoc}
     */
    public function help(oxIOutput $oOutput)
    {
        $oOutput->writeLn('Usage: module:list [options]');
        $oOutput->writeLn();
        $oOutput->writeLn('Outputs module id, module title and if it is active in current shop');
        $oOutput->writeLn();
        $oOutput->writeLn('Available options:');
        $oOutput->writeLn('  --shop=<shop_id>  Specifies in which shop to fix states');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(oxIOutput $oOutput)
    {
        try {
            $oConfig = $this->_parseShopConfig();
        } catch (oxConsoleException $oEx) {
            $oOutput->writeLn($oEx->getMessage());
            return;
        }

        $this->_printModulesTable($this->_getModules($oConfig), $oOutput);
    }

    /**
     * Print modules table
     *
     * @param oxModule[] $aModules
     * @param oxIOutput $oOutput
     */
    protected function _printModulesTable(array $aModules, oxIOutput $oOutput)
    {
        $iLang = oxRegistry::getLang()->getBaseLanguage();
        $sLine = str_repeat('=', 78);
        $sFormat = '| %30s | %35s | %3s |';

        $oOutput->writeLn($sLine);
        $oOutput->writeLn(sprintf($sFormat, 'ID', 'Title', 'Act'));
        $oOutput->writeLn($sLine);

        foreach ($aModules as $oModule) {
            $oOutput->writeLn(
                sprintf(
                    $sFormat,
                    $oModule->getInfo('id'),
                    $oModule->getInfo('title', $iLang),
                    ($oModule->isActive() ? 'Yes' : 'No')
                )
            );
        }

        $oOutput->writeLn($sLine);
    }

    /**
     * Get modules
     *
     * @param oxConfig $oConfig
     *
     * @return oxModule[]
     */
    protected function _getModules(oxConfig $oConfig)
    {
        $aModules = array();

        /** @var oxModule $oModule */
        $oModule = oxNew('oxModule');
        $oModule->setConfig($oConfig);

        foreach (array_keys($oModule->getModulePaths()) as $sModuleId) {
            $oModule->load($sModuleId);
            $aModules[] = clone $oModule;
        }

        return $aModules;
    }

    /**
     * Parse shop config instance from input
     *
     * @throws oxConsoleException
     *
     * @return oxConfig
     */
    protected function _parseShopConfig()
    {
        $oInput = $this->getInput();
        if ($oInput->hasOption('shop')) {
            $mShopId = $oInput->getOption('shop');
            if ($oConfig = oxSpecificShopConfig::get($mShopId)) {
                return $oConfig;
            }

            /** @var oxConsoleException $oEx */
            $oEx = oxNew('oxConsoleException');
            $oEx->setMessage('Shop with given id does not exist');
            throw $oEx;
        }

        return oxRegistry::getConfig();
    }
}