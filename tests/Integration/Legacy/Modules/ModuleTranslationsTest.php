<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;

class ModuleTranslationsTest extends BaseModuleTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testTranslation(): void
    {
        $this->installAndActivateModule('translation_Application');

        Registry::set(Language::class, null);

        $translatedGerman = Registry::getLang()->translateString('BIRTHDATE', 0);
        $translatedEnglish = Registry::getLang()->translateString('BIRTHDATE', 1);

        $this->assertEquals('MODUL: Geburtsdatum', $translatedGerman);
        $this->assertEquals('MODULE: Date of birth', $translatedEnglish);
    }
}
