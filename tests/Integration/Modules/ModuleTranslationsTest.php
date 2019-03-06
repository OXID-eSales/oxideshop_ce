<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;

/**
 * Test, that the translations in the modules work as expected.
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Modules
 */
class ModuleTranslationsTest extends BaseModuleTestCase
{
    /**
     * Test, that the translation of the modules are taken as we wish.
     */
    public function testTranslation()
    {
        $this->installAndActivateModule(oxNew(Module::class), 'translation_Application');

        // reset translations object
        Registry::set(\OxidEsales\Eshop\Core\Language::class, null);

        $translatedGerman = Registry::getLang()->translateString('BIRTHDATE', 0);
        $translatedEnglish = Registry::getLang()->translateString('BIRTHDATE', 1);

        $this->assertEquals('MODUL: Geburtsdatum', $translatedGerman);
        $this->assertEquals('MODULE: Date of birth', $translatedEnglish);
    }
}
