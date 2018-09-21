<?php

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Extension;

use OxidEsales\EshopCommunity\Internal\Twig\Extensions\OxidExtension;

class OxidIncludeWidget extends \OxidTestCase
{

    /**
     * @var OxidExtension
     */
    protected $extension;

    protected function setUp()
    {
        parent::setUp();
        $this->extension = new OxidExtension();
    }

    /**
     * Returns data for testSingleAssignment
     *
     * @return array
     */
    public function provider()
    {
        return array(
            ['FIRST_NAME', 'First name', 1, false],
            ['FIRST_NAME', 'Vorname', 0, false],
            ['GENERAL_SAVE', 'Save', 1, true],
            ['VAT', 'VAT', 1, false],
            ['GENERAL_SAVE', 'Speichern', 0, true]
        );
    }

    /**
     * Tests basic usage of oxmultilang smarty function
     *
     * @dataProvider provider
     */
    public function testSingleAssignment($sIndent, $sTranslation, $iLang, $blAdmin)
    {
        $this->setLanguage($iLang);
        $this->setAdminMode($blAdmin);
        $this->assertEquals($sTranslation, $this->extension->oxmultilang(['ident' => $sIndent]));
    }

    /**
     * Returns data for testAssignmentPlusSuffix
     *
     * @return array
     */
    public function additionalProvider()
    {
        return array(
            ['FIRST_NAME', 'First name:', 1, 'COLON'],
            ['FIRST_NAME', 'Vorname:', 0, 'COLON'],
            ['FIRST_NAME', 'First name!', 1, '!'],
            ['FIRST_NAME', 'Vorname !', 0, ' !']
        );
    }

    /**
     * Tests smarty oxmultilang smarty function assignment with suffixes
     *
     * @dataProvider additionalProvider
     */
    public function testAssignmentPlusSuffix($sIndent, $sTranslation, $iLang, $sSuffixIndent)
    {
        $this->setLanguage($iLang);
        $this->assertEquals($sTranslation, $this->extension->oxmultilang(['ident' => $sIndent, 'suffix' => $sSuffixIndent]));
    }

    /**
     * Returns data with alternative translations for testAlternativeAssignments
     *
     * @return array
     */
    public function alternativeProvider()
    {
        return [
            [
                ['ident' => 'FIRST_NAME_WRONG_HAS_ALTERNATIVE', 'alternative' => 'Alternative translation', 'suffix' => '!'],
                'Alternative translation!', 1
            ], // we can actually add any string at the end
            [
                ['ident' => 'FIRST_NAME_WRONG_HAS_ALTERNATIVE_NO_SUFFIX', 'alternative' => 'Vorname:'],
                'Vorname:', 0
            ]
        ];
    }

    /**
     * Test alternative translations and suffixes
     *
     * @dataProvider alternativeProvider
     */
    public function testAlternativeAssignments($aArgs, $sTranslation, $iLang)
    {
        $this->setLanguage($iLang);
        $this->assertEquals($sTranslation, $this->extension->oxmultilang($aArgs));
    }


    /**
     * testTranslateFrontend_isMissingTranslation data provider
     *
     * @return array
     */
    public function missingTranslationProviderFrontend()
    {
        return [
            [
                true,
                ['ident' => 'MY_MISING_TRANSLATION'],
                'MY_MISING_TRANSLATION'
            ],
            [
                false,
                ['ident' => 'MY_MISING_TRANSLATION'],
                'ERROR: Translation for MY_MISING_TRANSLATION not found!'
            ],
            [
                true,
                ['ident' => 'MY_MISING_TRANSLATION', 'noerror' => true],
                'MY_MISING_TRANSLATION'
            ],
            [
                false,
                ['ident' => 'MY_MISING_TRANSLATION', 'noerror' => true],
                'MY_MISING_TRANSLATION'
            ],
            [
                false,
                ['ident' => 'MY_MISING_TRANSLATION', 'noerror' => false],
                'ERROR: Translation for MY_MISING_TRANSLATION not found!'
            ]
        ];
    }

    /**
     * @dataProvider missingTranslationProviderFrontend
     */
    public function testTranslateFrontend_isMissingTranslation($isProductiveMode, $aArgs, $sTranslation)
    {
        $this->setAdminMode(false);

        $this->setLanguage(1);

        $oShop = $this->getConfig()->getActiveShop();
        $oShop->oxshops__oxproductive = new \oxfield($isProductiveMode);
        $oShop->save();

        $this->assertEquals($sTranslation, $this->extension->oxmultilang($aArgs));
    }

    /**
     * testTranslateAdmin_isMissingTranslation data provider
     *
     * @return array
     */
    public function missingTranslationProviderAdmin()
    {
        return [
            [
                ['ident' => 'MY_MISING_TRANSLATION'],
                'ERROR: Translation for MY_MISING_TRANSLATION not found!'
            ],
            [
                ['ident' => 'MY_MISING_TRANSLATION', 'noerror' => true],
                'MY_MISING_TRANSLATION'
            ],
            [
                ['ident' => 'MY_MISING_TRANSLATION', 'noerror' => false],
                'ERROR: Translation for MY_MISING_TRANSLATION not found!'
            ]
        ];
    }

    /**
     * @dataProvider missingTranslationProviderAdmin
     */
    public function testTranslateAdmin_isMissingTranslation($aArgs, $sTranslation)
    {
        $this->setLanguage(1);
        $this->setAdminMode(true);

        $this->assertEquals($sTranslation, $this->extension->oxmultilang($aArgs));
    }

}
