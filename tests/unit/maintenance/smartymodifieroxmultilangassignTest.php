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
 * @version   SVN: $Id: smartymodifieroxmultilangassignTest.php 56456 2013-03-08 14:54:00Z tadas.rimkus $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';
require_once oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'core/smarty/plugins/modifier.oxmultilangassign.php';

class Unit_Maintenance_smartyModifieroxmultilangassignTest extends OxidTestCase
{
    /**
     * Provides data to testSimpleAssignments
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array( 'FIRST_NAME', 0, 'Vorname' ),
            array( 'FIRST_NAME', 1, 'First name' )
        );
    }

    /**
     * Tests simple assignments, where only translation is fetched
     *
     * @dataProvider provider
     */
    public function testSimpleAssignments( $sIndent, $iLang, $sResult)
    {
        $this->setLanguage( $iLang );
        $this->assertEquals( $sResult, smarty_modifier_oxmultilangassign( $sIndent ) );
    }

    /**
     * Provides data to testArgumentedAssignments
     *
     * @return array
     */
    public function argumentedProvider()
    {
        return array(
            array( 'MANUFACTURER_S', 0, 'Opel', '| Hersteller: Opel' ),
            array( 'MANUFACTURER_S', 1, 'Opel', 'Manufacturer: Opel' ),
            array( 'EMAIL_INVITE_HTML_INVITETOSHOP', 0, array( 'Admin', 'OXID Shop' ), 'Eine Einladung von Admin OXID Shop zu besuchen.' ),
            array( 'EMAIL_INVITE_HTML_INVITETOSHOP', 1, array( 'Admin', 'OXID Shop' ), 'An invitation from Admin to visit OXID Shop' )
        );
    }

    /**
     * Tests value assignments when translating strings containing %s
     *
     * @dataProvider argumentedProvider
     */
    public function testArgumentedAssignments( $sIndent, $iLang, $aArgs, $sResult )
    {
        $this->setLanguage( $iLang );
        $this->assertEquals( $sResult, smarty_modifier_oxmultilangassign( $sIndent, $aArgs ) );
    }
}
