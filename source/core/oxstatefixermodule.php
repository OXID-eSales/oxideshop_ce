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
 * State fixer module
 *
 * Extension of regular module class to add module information
 * fixer features
 */
class oxStateFixerModule extends oxModule
{

    /**
     * Fix module states task runs version, extend, files, templates, blocks,
     * settings and events information fix tasks
     *
     * @param oxConfig|null $oConfig If not passed uses default base shop config
     */
    public function fix(oxConfig $oConfig = null)
    {
        if ($oConfig === null) {
            $this->setConfig($oConfig);
        }

        $this->fixVersion();
        $this->fixExtend();
        $this->fixFiles();
        $this->fixTemplates();
        $this->fixBlocks();
        $this->fixSettings();
        $this->fixEvents();
    }

    /**
     * Fix module version
     */
    public function fixVersion()
    {
        $sVersion = $this->getInfo('version');
        $this->_addModuleVersion($sVersion, $this->getId());
    }

    /**
     * Fix extension chain of module
     */
    public function fixExtend()
    {
        $aExtend = $this->getInfo('extend');
        $this->_setModuleExtend($this->getId(), $aExtend);
    }

    /**
     * Fix files
     */
    public function fixFiles()
    {
        $aFiles = $this->getInfo('files');
        $this->_addModuleFiles($aFiles, $this->getId());
    }

    /**
     * Fix templates
     */
    public function fixTemplates()
    {
        $aTemplates = $this->getInfo('templates');
        $this->_addTemplateFiles($aTemplates, $this->getId());
    }

    /**
     * Fix blocks
     */
    public function fixBlocks()
    {
        $this->_deleteModuleBlockEntries();

        $aBlocks = $this->getInfo('blocks');
        $this->_addTemplateBlocks($aBlocks, $this->getId());
    }

    /**
     * Delete module block entries
     *
     * @codeCoverageIgnore
     */
    protected function _deleteModuleBlockEntries()
    {
        $sShopId = $this->getConfig()->getShopId();
        $sModuleId = $this->getId();
        $oDb = oxDb::getDb();

        $sSql = 'DELETE FROM oxtplblocks WHERE oxmodule = %s AND oxshopid = %s';
        $oDb->execute(sprintf($sSql, $oDb->quote($sModuleId), $oDb->quote($sShopId)));
    }

    /**
     * Fix settings
     */
    public function fixSettings()
    {
        $this->_deleteModuleSettingEntries();

        $aSettings = $this->getInfo('settings');
        $this->_addModuleSettings($aSettings, $this->getId());
    }

    /**
     * Delete module setting entries
     *
     * @codeCoverageIgnore
     */
    protected function _deleteModuleSettingEntries()
    {
        $sShopId = $this->getConfig()->getShopId();
        $sModuleId = $this->getId();
        $oDb = oxDb::getDb();

        $sSql = 'DELETE FROM oxconfig WHERE oxmodule = %s AND oxshopid = %s';
        $oDb->execute(sprintf($sSql, $oDb->quote('module' . $sModuleId), $oDb->quote($sShopId)));

        $sSql = 'DELETE FROM oxconfigdisplay WHERE oxcfgmodule = %s';
        $oDb->execute(sprintf($sSql, $oDb->quote($sModuleId)));
    }

    /**
     * Fix events
     */
    public function fixEvents()
    {
        $aEvents = $this->getInfo('events');
        $this->_addModuleEvents($aEvents, $this->getId());
    }

    /**
     * Set template extend to database, do cleanup before.
     *
     * @author Alfonsas Cirtautas
     *
     * @param string $sModuleId Module ID
     * @param array $aModuleExtend Extend data array from metadata
     */
    protected function _setModuleExtend($sModuleId, $aModuleExtend)
    {
        $aInstalledModules = $this->getAllModules();
        $sModulePath = $this->getModulePath($sModuleId);

        // Remove extended modules by path
        if ($sModulePath && is_array($aInstalledModules)) {
            foreach ($aInstalledModules as $sClassName => $mModuleName) {
                if (!is_array($mModuleName)) {
                    continue;
                }

                foreach ($mModuleName as $sKey => $sModuleName) {
                    if (strpos($sModuleName, $sModulePath . '/') === 0) {
                        unset($aInstalledModules[$sClassName][$sKey]);
                    }
                }
            }
        }

        $aModules = $this->mergeModuleArrays($aInstalledModules, $aModuleExtend);
        $aModules = $this->buildModuleChains($aModules);

        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aModules', $aModules);
        $oConfig->saveShopConfVar('aarr', 'aModules', $aModules);
    }
}