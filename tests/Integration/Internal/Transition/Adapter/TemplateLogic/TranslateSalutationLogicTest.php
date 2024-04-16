<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateSalutationLogic;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator\LegacyTemplateTranslator;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class TranslateSalutationLogicTest extends IntegrationTestCase
{
    public static function translateSalutationProvider(): array
    {
        return [
            ['MR', 0, 'Herr'],
            ['MRS', 0, 'Frau'],
            ['MR', 1, 'Mr'],
            ['MRS', 1, 'Mrs']
        ];
    }


    #[DataProvider('translateSalutationProvider')]
    public function testTranslateSalutation(string $ident, int $languageId, string $expected): void
    {
        $translateSalutationLogic = new TranslateSalutationLogic($this->getTranslator($languageId));
        $this->assertEquals($expected, $translateSalutationLogic->translateSalutation($ident));
    }

    private function getTranslator(int $languageId): LegacyTemplateTranslator
    {
        $language = new Language();
        $language->setTplLanguage($languageId);
        $language->setAdminMode(false);
        return new LegacyTemplateTranslator($language);
    }
}
