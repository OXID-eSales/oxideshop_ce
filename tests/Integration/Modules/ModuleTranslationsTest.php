<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\Eshop\Core\Registry;

/**
 * Test, that the translations in the modules work as expected.
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Modules
 */
class ModuleTranslationsTest extends BaseModuleTestCase
{
    /**
     * Data provider for the translation testing method.
     *
     * @return array The test cases we want to
     */
    public function providerTranslation()
    {
        return array(
            /*
                Standard shop translation - cause the environment object changes the config option "sShopDir",
                we don't get the real shop translations here
            */
            array(
                'activatedModule' => array(),
                'expectedTranslations' => array(0 => 'BIRTHDATE', 1 => 'BIRTHDATE')
            ),
            array(
                'activatedModule' => array('translation_Application'),
                'expectedTranslations' => array(0 => 'MODUL: Geburtsdatum', 1 => 'MODULE: Date of birth')
            )
        );
    }

    /**
     * Test, that the translation of the modules are taken as we wish.
     *
     * @dataProvider providerTranslation
     *
     * @param array  $activatedModule      The module we want to activate.
     * @param string $expectedTranslations The translation we want to get from the language object.
     */
    public function testTranslation($activatedModule, $expectedTranslations)
    {
        $oEnvironment = new Environment();
        $oEnvironment->prepare($activatedModule);

        // reset translations object
        Registry::set(\OxidEsales\Eshop\Core\Language::class, null);

        $translatedGerman = Registry::getLang()->translateString('BIRTHDATE', 0);
        $translatedEnglish = Registry::getLang()->translateString('BIRTHDATE', 1);

        $this->assertEquals($expectedTranslations[0], $translatedGerman);
        $this->assertEquals($expectedTranslations[1], $translatedEnglish);
    }
}
