<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use \Smarty;
use \oxField;
use \oxRegistry;

$filePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/function.oxmultilang.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/function.oxmultilang.php';
}

class SmartyFunctionOxMultiLangTest extends \OxidTestCase
{

    /**
     * Returns data for testSingleAssignment
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array('FIRST_NAME', 'First name', 1, false),
            array('FIRST_NAME', 'Vorname', 0, false),
            array('GENERAL_SAVE', 'Save', 1, true),
            array('VAT', 'VAT', 1, false),
            array('GENERAL_SAVE', 'Speichern', 0, true),
        );
    }

    /**
     * Tests basic usage of oxmultilang smarty function
     *
     * @dataProvider provider
     */
    public function testSingleAssignment($sIndent, $sTranslation, $iLang, $blAdmin)
    {
        $oSmarty = new Smarty();
        $this->setLanguage($iLang);
        $this->setAdminMode($blAdmin);
        $this->assertEquals($sTranslation, smarty_function_oxmultilang(array('ident' => $sIndent), $oSmarty));
    }

    /**
     * Returns data for testAssignmentPlusSuffix
     *
     * @return array
     */
    public function additionalProvider()
    {
        return array(
            array('FIRST_NAME', 'First name:', 1, 'COLON'),
            array('FIRST_NAME', 'Vorname:', 0, 'COLON'),
            array('FIRST_NAME', 'First name!', 1, '!'),
            array('FIRST_NAME', 'Vorname !', 0, ' !')
        );
    }

    /**
     * Tests smarty oxmultilang smarty function assignment with suffixes
     *
     * @dataProvider additionalProvider
     */
    public function testAssignmentPlusSuffix($sIndent, $sTranslation, $iLang, $sSuffixIndent)
    {
        $oSmarty = new Smarty();
        $this->setLanguage($iLang);
        $this->assertEquals($sTranslation, smarty_function_oxmultilang(array('ident' => $sIndent, 'suffix' => $sSuffixIndent), $oSmarty));
    }

    /**
     * Returns data with alternative translations for testAlternativeAssignments
     *
     * @return array
     */
    public function alternativeProvider()
    {
        return array(
            array(
                array('ident' => 'FIRST_NAME_WRONG_HAS_ALTERNATIVE', 'alternative' => 'Alternative translation', 'suffix' => '!'),
                'Alternative translation!', 1), // we can actually add any string at the end
            array(
                array('ident' => 'FIRST_NAME_WRONG_HAS_ALTERNATIVE_NO_SUFFIX', 'alternative' => 'Vorname:'),
                'Vorname:', 0),
        );
    }

    /**
     * Test alternative translations and suffixes
     *
     * @dataProvider alternativeProvider
     */
    public function testAlternativeAssignments($aArgs, $sTranslation, $iLang)
    {
        $oSmarty = new Smarty();

        $this->setLanguage($iLang);
        $this->assertEquals($sTranslation, smarty_function_oxmultilang($aArgs, $oSmarty));
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
                array('ident' => 'MY_MISING_TRANSLATION'),
                'MY_MISING_TRANSLATION',
            ),
            array(
                false,
                array('ident' => 'MY_MISING_TRANSLATION'),
                'ERROR: Translation for MY_MISING_TRANSLATION not found!',
            ),
            array(
                true,
                array('ident' => 'MY_MISING_TRANSLATION', 'noerror' => true),
                'MY_MISING_TRANSLATION',
            ),
            array(
                false,
                array('ident' => 'MY_MISING_TRANSLATION', 'noerror' => true),
                'MY_MISING_TRANSLATION',
            ),
            array(
                false,
                array('ident' => 'MY_MISING_TRANSLATION', 'noerror' => false),
                'ERROR: Translation for MY_MISING_TRANSLATION not found!',
            ),
        );
    }

    /**
     * @dataProvider missingTranslationProviderFrontend
     */
    public function testTranslateFrontend_isMissingTranslation($isProductiveMode, $aArgs, $sTranslation)
    {
        $this->setAdminMode(false);
        $oSmarty = new Smarty();

        $this->setLanguage(1);

        $oShop = $this->getConfig()->getActiveShop();
        $oShop->oxshops__oxproductive = new oxField($isProductiveMode);
        $oShop->save();

        $this->assertEquals($sTranslation, smarty_function_oxmultilang($aArgs, $oSmarty));
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
                array('ident' => 'MY_MISING_TRANSLATION'),
                'ERROR: Translation for MY_MISING_TRANSLATION not found!',
            ),
            array(
                array('ident' => 'MY_MISING_TRANSLATION', 'noerror' => true),
                'MY_MISING_TRANSLATION',
            ),
            array(
                array('ident' => 'MY_MISING_TRANSLATION', 'noerror' => false),
                'ERROR: Translation for MY_MISING_TRANSLATION not found!',
            ),
        );
    }

    /**
     * @dataProvider missingTranslationProviderAdmin
     */
    public function testTranslateAdmin_isMissingTranslation($aArgs, $sTranslation)
    {
        $oSmarty = new Smarty();

        $this->setLanguage(1);
        $this->setAdminMode(true);

        $this->assertEquals($sTranslation, smarty_function_oxmultilang($aArgs, $oSmarty));
    }
}
