<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once oxRegistry::getConfig()->getConfigParam('sShopDir') . 'core/smarty/plugins/modifier.oxmultilangassign.php';

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
            array('FIRST_NAME', 0, 'Vorname'),
            array('FIRST_NAME', 1, 'First name'),
            array('VAT', 1, 'VAT')

        );
    }

    /**
     * Tests simple assignments, where only translation is fetched
     *
     * @dataProvider provider
     */
    public function testSimpleAssignments($sIndent, $iLang, $sResult)
    {
        $this->setLanguage($iLang);
        $this->assertEquals($sResult, smarty_modifier_oxmultilangassign($sIndent));
    }

    /**
     * Provides data to testAssignmentsWithArguments
     *
     * @return array
     */
    public function withArgumentsProvider()
    {
        return array(
            array('MANUFACTURER_S', 0, 'Opel', '| Hersteller: Opel'),
            array('MANUFACTURER_S', 1, 'Opel', 'Manufacturer: Opel'),
            array('INVITE_TO_SHOP', 0, array('Admin', 'OXID Shop'), 'Eine Einladung von Admin OXID Shop zu besuchen.'),
            array('INVITE_TO_SHOP', 1, array('Admin', 'OXID Shop'), 'An invitation from Admin to visit OXID Shop')
        );
    }

    /**
     * Tests value assignments when translating strings containing %s
     *
     * @dataProvider withArgumentsProvider
     */
    public function testAssignmentsWithArguments($sIndent, $iLang, $aArgs, $sResult)
    {
        $this->setLanguage($iLang);
        $this->assertEquals($sResult, smarty_modifier_oxmultilangassign($sIndent, $aArgs));
    }

    /**
     * testTranslateFrontend_isMissingTranslation data provider
     *
     * @return array
     */
    public function missingTranslationProviderFrontend()
    {
        return array(
            array(
                true,
                'MY_MISING_TRANSLATION',
                'MY_MISING_TRANSLATION',
            ),
            array(
                false,
                'ident' => 'MY_MISING_TRANSLATION',
                'ERROR: Translation for MY_MISING_TRANSLATION not found!',
            ),
        );
    }

    /**
     * @dataProvider missingTranslationProviderFrontend
     */
    public function testTranslateFrontend_isMissingTranslation($isProductiveMode, $sIndent, $sTranslation)
    {
        $this->setAdminMode(false);
        $oSmarty = new Smarty();

        $this->setLanguage(1);

        $oShop = $this->getConfig()->getActiveShop();
        $oShop->oxshops__oxproductive = new oxField($isProductiveMode);
        $oShop->save();

        $this->assertEquals($sTranslation, smarty_modifier_oxmultilangassign($sIndent, $oSmarty));
    }

    /**
     * testTranslateAdmin_isMissingTranslation data provider
     *
     * @return array
     */
    public function missingTranslationProviderAdmin()
    {
        return array(
            array(
                'MY_MISING_TRANSLATION',
                'ERROR: Translation for MY_MISING_TRANSLATION not found!',
            ),
        );
    }

    /**
     * @dataProvider missingTranslationProviderAdmin
     */
    public function testTranslateAdmin_isMissingTranslation($sIdent, $sTranslation)
    {
        $oSmarty = new Smarty();

        $this->setLanguage(1);
        $this->setAdminMode(true);

        $this->assertEquals($sTranslation, smarty_modifier_oxmultilangassign($sIdent, $oSmarty));
    }
}
