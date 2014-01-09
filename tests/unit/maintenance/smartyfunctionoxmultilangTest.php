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
 * @version   SVN: $Id: smartyfunctionoxmultilangTest.php 56456 2013-03-08 14:54:00Z tadas.rimkus $
 */
require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';
require_once oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'core/smarty/plugins/function.oxmultilang.php';

class smartyFunctionoxmultilangTest extends OxidTestCase
{

    /**
     * Returns data for testSingleAssignment
     *
     * @return array
     */
    public function provider() {
        return array (
            array( 'FIRST_NAME', 'First name', 1),
            array( 'FIRST_NAME', 'Vorname', 0)
        );
    }

    /**
     * Tests basic usage of oxmultilang smarty function
     *
     * @dataProvider provider
     */
    public function testSingleAssignment( $sIndent, $sTranslation, $iLang ) {
        $oSmarty = new Smarty();

        $this->setLanguage( $iLang );
        $this->assertEquals( $sTranslation, smarty_function_oxmultilang( array( 'ident' => $sIndent ), $oSmarty ) );
    }

    /**
     * Returns data for testAssignmentPlusSuffix
     *
     * @return array
     */
    public function additionalProvider() {
        return array (
            array( 'FIRST_NAME', 'First name:', 1, 'COLON'),
            array( 'FIRST_NAME', 'Vorname:', 0, 'COLON'),
            array( 'FIRST_NAME', 'First name!', 1, '!'),
            array( 'FIRST_NAME', 'Vorname !', 0, ' !')
        );
    }

    /**
     * Tests smarty oxmultilang smarty function assignment with suffixes
     *
     * @dataProvider additionalProvider
     */
    public function testAssignmentPlusSuffix( $sIndent, $sTranslation, $iLang, $sSuffixIndent )
    {
        $oSmarty = new Smarty();

        $this->setLanguage( $iLang );
        $this->assertEquals( $sTranslation, smarty_function_oxmultilang( array( 'ident' => $sIndent, 'suffix' => $sSuffixIndent ), $oSmarty ) );
    }

    /**
     *
     * Returns data with alternative translations for testAlternativeAssignments
     *
     * @return array
     */
    public function alternativeProvider()
    {
        return array (
            array(
                array( 'ident' => 'FIRST_NAME', 'suffix' => 'COLON' ),
                'First name:', 1),
            array(
                array( 'ident' => 'FIRST_NAME_WRONG_NOALTERNATIVE', 'suffix' => 'COLON' ),
                'FIRST_NAME_WRONG_NOALTERNATIVE:', 0),
            array(
                array( 'ident' => 'FIRST_NAME_WRONG_HAS_ALTERNATIVE', 'alternative' => 'Alternative translation', 'suffix' => '!' ),
                'Alternative translation!', 1), // we can actually add any string at the end
            array(
                array( 'ident' => 'FIRST_NAME_WRONG_HAS_ALTERNATIVE_NO_SUFFIX', 'alternative' => 'Vorname:'  ),
                'Vorname:', 0),
            array(
                array( 'ident' => 'VAT_PLUS_PERCENT_AMOUNT', 'args' => '19'  ),
                'plus VAT 19% Amount', 1),
            array(
                array( 'ident' => 'VAT_PLUS_PERCENT_AMOUNT', 'args' => 0  ),
                'plus VAT 0% Amount', 1)
        );
    }

    /**
     * Test alternative translations and suffixes
     *
     * @dataProvider alternativeProvider
     */
    public function testAlternativeAssignments( $aArgs, $sTranslation, $iLang )
    {
        $oSmarty = new Smarty();

        $this->setLanguage( $iLang );
        $this->assertEquals( $sTranslation, smarty_function_oxmultilang( $aArgs, $oSmarty ) );
    }
}
