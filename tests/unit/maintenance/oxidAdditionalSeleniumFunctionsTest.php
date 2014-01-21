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
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';
require_once realpath( "." ).'/acceptance/oxidAdditionalSeleniumFunctions.php';

class Acceptance_oxidAdditionalSeleniumFunctionsTest extends OxidTestCase
{

    /**
     * Translate string with default parameters
     * Default language is null, so it is german
     * Admin mode default is null, so it is frontend
     */
    public function testTranslateDefault()
    {
        $oOxidSeleniumFunctions = new oxidAdditionalSeleniumFunctions();
        $this->assertEquals( 'Konto', $oOxidSeleniumFunctions->translateString( 'ACCOUNT' ) );
    }


    /**
     * Provided data for translateString test
     *
     * @return array
     */
    public function translationProvider()
    {
        return array(
            array( 'ACCOUNT', 0, false, false, 'Konto' ),                   // german translation
            array( 'ACCOUNT', null, null, false, 'Konto' ),                 // german default translation
            array( 'ACCOUNT', 1, false, false, 'Account' ),                 // english non admin translation
            array( 'ACCOUNT', 0, true, false, 'ACCOUNT' ),                  // no translation in admin
            array( 'ACCOUNT', 1, true, false, 'ACCOUNT' ),                  // no translation in admin
            array( 'GENERAL_ACTIVE', 0, true, false, 'Aktiv' ),             // German translation in admin
            array( 'GENERAL_ACTIVE', 1, true, false, 'Active' ),            // English translation in admin
            array( 'GENERAL_ACTIVE', 0, false, false, 'GENERAL_ACTIVE' ),   // no translation in frontend, only admin
            array( 'GENERAL_ACTIVE', 0, false, false, 'GENERAL_ACTIVE' ),   // no translation in frontend, only admin
            array( 'GENERAL_ACTIVE', null, null, false, 'GENERAL_ACTIVE' ), // no translation in frontend, only admin
        );
    }

    /**
     * Try translate string method with different parameters
     *
     * @dataProvider translationProvider
     */
    public function testTranslate( $sKey, $iLang, $blAdmin, $blFailOnNonExisting, $sExpectedTranslation )
    {
        $oOxidSeleniumFunctions = new oxidAdditionalSeleniumFunctions();
        $this->assertEquals( $sExpectedTranslation, $oOxidSeleniumFunctions->translateString( $sKey, $iLang, $blAdmin, $blFailOnNonExisting ) );
    }

    public function failingTranslationProvider()
    {
        return array(
            array( 'ACCOUNT', 0, true ),           // no translation in admin
            array( 'ACCOUNT', 1, true ),           // no translation in admin
            array( 'GENERAL_ACTIVE', 0, false ),   // no translation in frontend, only admin
            array( 'GENERAL_ACTIVE', 0, false ),   // no translation in frontend, only admin
            array( 'GENERAL_ACTIVE', null, null ), // no translation in frontend, only admin
        );
    }

    /**
     * Try to translate non existing string with failure
     *
     * @dataProvider failingTranslationProvider
     */
    public function testTranslateFails( $sKey, $iLang, $blAdmin )
    {
        $this->setExpectedException( 'PHPUnit_Framework_AssertionFailedError' );
        $oOxidSeleniumFunctions = new oxidAdditionalSeleniumFunctions();
        $oOxidSeleniumFunctions->translateString( $sKey, $iLang, $blAdmin );
    }
}
