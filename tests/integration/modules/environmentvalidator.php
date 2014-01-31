<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

require_once realpath(dirname(__FILE__).'/../../') . '/unit/OxidTestCase.php';

class EnvironmentValidator {

    protected $_oConfig;

    /**
     * @param mixed $oConfig
     */
    public function setConfig( $oConfig )
    {
        $this->_oConfig = $oConfig;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->_oConfig;
    }

    /**
     * Asserts that module templates match expected templates
     *
     * @param $aExpectedTemplates
     * @return bool
     */
    public function checkTemplates( $aExpectedTemplates )
    {
        $aTemplatesToCheck = $this->getConfig()->getConfigParam( 'aModuleTemplates' );
        $aTemplatesToCheck = is_null( $aTemplatesToCheck ) ? array() : $aTemplatesToCheck;

        return ( $aExpectedTemplates == $aTemplatesToCheck );
    }

    /**
     * Asserts that module blocks match expected blocks
     *
     * @param $aExpectedBlocks
     * @return bool
     */
    public function checkBlocks( $aExpectedBlocks )
    {
        $oDb = oxDb::getDb();
        $aBlocksToCheck = $oDb->getAll( 'select * from oxtplblocks' );

        return ( count( $aExpectedBlocks ) == count( $aBlocksToCheck ) );
    }

    /**
     * Asserts that module extensions match expected extensions
     *
     * @param $aExpectedExtensions
     * @return bool
     */
    public function checkExtensions( $aExpectedExtensions )
    {
        $aExtensionsToCheck = $this->getConfig()->getConfigParam( 'aModules' );

        return ( $aExpectedExtensions == $aExtensionsToCheck );
    }

    /**
     * Asserts that disabled module is in disabled modules list
     *
     * @param $aExpectedDisabledModules
     * @return bool
     */
    public function checkDisabledModules( $aExpectedDisabledModules )
    {
        $aDisabledModules = $this->getConfig()->getConfigParam( 'aDisabledModules' );

        return ( $aExpectedDisabledModules == $aDisabledModules );
    }

    /**
     * Asserts that module files match expected files
     *
     * @param $aExpectedFiles
     * @return bool
     */
    public function checkFiles( $aExpectedFiles )
    {
        $aModuleFilesToCheck = $this->getConfig()->getConfigParam( 'aModuleFiles' );
        $aModuleFilesToCheck = is_null( $aModuleFilesToCheck ) ? array() : $aModuleFilesToCheck;

        return ( $aExpectedFiles == $aModuleFilesToCheck );
    }

    /**
     * Asserts that module configs match expected configs
     *
     * @param $aExpectedConfigs
     * @return bool
     */
    public function checkConfigAmount( $aExpectedConfigs )
    {
        $oDb = oxDb::getDb(  );
        $aConfigsToCheck = $oDb->getAll(
            "select c.oxvarname
            from  oxconfig c inner join oxconfigdisplay d
                on c.oxvarname = d.oxcfgvarname  and c.oxmodule = d.oxcfgmodule
            where oxmodule like 'module:%'" );

        return ( count($aExpectedConfigs) == count($aConfigsToCheck) );
    }

    /**
     * Asserts that module version match expected version
     *
     * @param $aExpectedVersions
     * @return bool
     */
    public function checkVersions( $aExpectedVersions )
    {
        $aModuleVersionsToCheck = $this->getConfig()->getConfigParam( 'aModuleVersions' );
        $aModuleVersionsToCheck = is_null( $aModuleVersionsToCheck ) ? array() : $aModuleVersionsToCheck;

        return ( $aExpectedVersions == $aModuleVersionsToCheck );
    }


    /**
     * Asserts that module version match expected version
     *
     * @param $aExpectedEvents
     * @return bool
     */
    public function checkEvents( $aExpectedEvents )
    {
        $aModuleEventsToCheck = $this->getConfig()->getConfigParam( 'aModuleEvents' );
        $aModuleEventsToCheck = is_null( $aModuleEventsToCheck) ? array() : $aModuleEventsToCheck;

        return ( $aExpectedEvents == $aModuleEventsToCheck );
    }

}