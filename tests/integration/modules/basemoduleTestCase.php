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
 */

require_once realpath( dirname(__FILE__).'/../../' ) . '/unit/OxidTestCase.php';
require_once realpath( dirname(__FILE__) ) . '/environmentvalidator.php';
require_once realpath( dirname(__FILE__) ) . '/environment.php';

class BaseModuleTestCase extends OxidTestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->clean();
        parent::tearDown();
    }

    /**
     * Runs all asserts
     *
     * @param $aExpectedResult
     * @param $sModuleId
     */
    protected function _runAsserts( $aExpectedResult, $sModuleId )
    {
        $oValidator = new EnvironmentValidator();
        $oValidator->setConfig( $this->getConfig() );

        if( isset( $aExpectedResult['blocks'] ) ){
            $this->assertTrue( $oValidator->checkBlocks( $aExpectedResult['blocks']), 'Blocks do not match expectations' );
        }

        if( isset( $aExpectedResult['extend'] ) ){
            $this->assertTrue( $oValidator->checkExtensions( $aExpectedResult['extend']), 'Extensions do not match expectations' );
        }

        if( isset( $aExpectedResult['files'] ) ){
            $this->assertTrue( $oValidator->checkFiles( $aExpectedResult['files']), 'Files do not match expectations' );
        }

        if( isset( $aExpectedResult['events'] ) ){
            $this->assertTrue( $oValidator->checkEvents( $aExpectedResult['events']), 'Events do not match expectations' );
        }

        if( isset( $aExpectedResult['settings'] ) ){
            $this->assertTrue( $oValidator->checkConfigAmount( $aExpectedResult['settings'], $sModuleId), 'Configs do not match expectations' );
        }

        if( isset( $aExpectedResult['versions'] ) ){
            $this->assertTrue( $oValidator->checkVersions( $aExpectedResult['versions']), 'Versions do not match expectations' );
        }

        if( isset( $aExpectedResult['templates'] ) ){
            $this->assertTrue( $oValidator->checkTemplates( $aExpectedResult['templates']), 'Templates do not match expectations' );
        }

        if( isset( $aExpectedResult['disabledModules'] ) ){
            $this->assertTrue( $oValidator->checkDisabledModules( $aExpectedResult['disabledModules'] ), 'Disabled modules do not match expectations' );
        }

        if ( isset( $aExpectedResult['settings_values'] ) ) {
            $this->assertTrue( $oValidator->checkConfigValues( $aExpectedResult['settings_values'], $sModuleId )
                , 'Config values does not match expectations' );
        }
    }

}
