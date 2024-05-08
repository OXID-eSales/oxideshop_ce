<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFilterLogic;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator\LegacyTemplateTranslator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class TranslateLogicTest extends IntegrationTestCase
{
    use ProphecyTrait;

    /**
     * Provides data to testSimpleAssignments
     *
     * @return array
     */
    public static function provider(): array
    {
        return [
            ['FIRST_NAME', 0, 'Vorname'],
            ['FIRST_NAME', 1, 'First name'],
            ['VAT', 1, 'VAT']
        ];
    }

    /**
     * Tests simple assignments, where only translation is fetched
     *
     * @param string $ident
     * @param int    $languageId
     * @param string $result
     *
     * @dataProvider provider
     */
    public function testSimpleAssignments($ident, $languageId, $result)
    {
        $multiLangFilterLogic = new TranslateFilterLogic($this->getContextMock(), $this->getTranslator($languageId));

        $this->assertEquals($result, $multiLangFilterLogic->multiLang($ident));
    }

    /**
     * Provides data to testAssignmentsWithArguments
     *
     * @return array
     */
    public static function withArgumentsProvider(): array
    {
        return [
            ['MANUFACTURER_S', 0, 'Opel', '| Hersteller: Opel'],
            ['MANUFACTURER_S', 1, 'Opel', 'Manufacturer: Opel'],
            ['INVITE_TO_SHOP', 0, ['Admin', 'OXID Shop'], 'Eine Einladung von Admin OXID Shop zu besuchen.'],
            ['INVITE_TO_SHOP', 1, ['Admin', 'OXID Shop'], 'An invitation from Admin to visit OXID Shop']
        ];
    }

    /**
     * @dataProvider withArgumentsProvider
     */
    public function testAssignmentsWithArguments(string $ident, int $languageId, $arguments, string $result): void
    {
        $multiLangFilterLogic = new TranslateFilterLogic($this->getContextMock(), $this->getTranslator($languageId));

        $this->assertEquals($result, $multiLangFilterLogic->multiLang($ident, $arguments));
    }

    public static function missingTranslationProviderFrontend(): array
    {
        return [
            [
                true,
                'MY_MISING_TRANSLATION',
                'MY_MISING_TRANSLATION',
            ],
            [
                false,
                'MY_MISING_TRANSLATION',
                'ERROR: Translation for MY_MISING_TRANSLATION not found!',
            ],
        ];
    }

    /**
     * @dataProvider missingTranslationProviderFrontend
     */
    public function testTranslateFrontend_isMissingTranslation(
        bool $isProductiveMode,
        string $ident,
        string $translation
    ): void
    {
        $context = $this->prophesize(ContextInterface::class);
        $context->isShopInProductiveMode()->willReturn($isProductiveMode);
        $multiLangFilterLogic = new TranslateFilterLogic($context->reveal(), $this->getTranslator(1));

        $this->assertEquals($translation, $multiLangFilterLogic->multiLang($ident));
    }

    private function getContextMock(): ContextInterface
    {
        return $this->getMockBuilder(ContextInterface::class)->getMock();
    }

    private function getTranslator(int $languageId): LegacyTemplateTranslator
    {
        $language = new Language();
        $language->setTplLanguage($languageId);
        $language->setAdminMode(false);
        return new LegacyTemplateTranslator($language);
    }
}
