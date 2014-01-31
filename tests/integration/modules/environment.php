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

class Environment
{
    /**
     * Loads and activates modules by given IDs.
     *
     * @param null $aModules
     * @throws Exception
     */
    public function prepare( $aModules = null )
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam( 'sShopDir', $this->_getPathToTestDataDirectory() );

        if ( is_null( $aModules ) ) {
            $aModules = $this->_getAllModules();
        }

        $this->clean();
        $this->_activateModules( $aModules );

    }

    /**
     * Cleans modules environment.
     */
    public function clean()
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam( 'aModules', null);
        $oConfig->setConfigParam( 'aModuleTemplates', null);
        $oConfig->setConfigParam( 'aDisabledModules', array() );
        $oConfig->setConfigParam( 'aModuleFiles', null);
        $oConfig->setConfigParam( 'aModuleVersions', null);
        $oConfig->setConfigParam( 'aModuleEvents', null);

        $oDb = oxDb::getDb();
        $oDb->execute( "DELETE FROM `oxconfig` WHERE `oxmodule` LIKE 'module:%'" );
        $oDb->execute( 'TRUNCATE `oxconfigdisplay`' );
        $oDb->execute( 'TRUNCATE `oxtplblocks`' );
    }

    /**
     * Returns fixtures directory.
     *
     * @return string
     */
    private function _getPathToTestDataDirectory()
    {
        return realpath( dirname( __FILE__ ) ) . '/testData/';
    }

    /**
     * Scans directory and returns modules IDs.
     *
     * @return array
     */
    private function _getAllModules()
    {
        $aModules = array_diff( scandir( $this->_getPathToTestDataDirectory() . 'modules' ), array( '..', '.' ) );

        return $aModules;
    }

    /**
     * Activates given modules.
     *
     * @param $aModules
     * @throws Exception
     */
    private function _activateModules( $aModules )
    {
        $oModule = new oxModule();
        foreach ( $aModules as $sModuleId ) {
            if ( !$oModule->load( $sModuleId ) || !$oModule->activate() ) {
                throw new Exception( "Module $sModuleId was not activated." );
            }
        }
    }
}