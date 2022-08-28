<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Language;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFilterLogic;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator\LegacyTemplateTranslator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class TranslateLogicTest extends UnitTestCase
{
    use ProphecyTrait;

    /**
     * Provides data to testSimpleAssignments
     *
     * @return array
     */
    public function provider(): array
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
    public function withArgumentsProvider(): array
    {
        return [
            ['MANUFACTURER_S', 0, 'Opel', '| Hersteller: Opel'],
            ['MANUFACTURER_S', 1, 'Opel', 'Manufacturer: Opel'],
            ['INVITE_TO_SHOP', 0, ['Admin', 'OXID Shop'], 'Eine Einladung von Admin OXID Shop zu besuchen.'],
            ['INVITE_TO_SHOP', 1, ['Admin', 'OXID Shop'], 'An invitation from Admin to visit OXID Shop']
        ];
    }

    /**
     * Tests value assignments when translating strings containing %s
     *
     * @param string $ident
     * @param int    $languageId
     * @param mixed  $arguments
     * @param string $result
     *
     * @dataProvider withArgumentsProvider
     */
    public function testAssignmentsWithArguments($ident, $languageId, $arguments, $result)
    {
        $multiLangFilterLogic = new TranslateFilterLogic($this->getContextMock(), $this->getTranslator($languageId));

        $this->assertEquals($result, $multiLangFilterLogic->multiLang($ident, $arguments));
    }

    /**
     * testTranslateFrontend_isMissingTranslation data provider
     *
     * @return array
     */
    public function missingTranslationProviderFrontend(): array
    {
        return [
            [
                true,
                'MY_MISING_TRANSLATION',
                'MY_MISING_TRANSLATION',
            ],
            [
                false,
                'ident' => 'MY_MISING_TRANSLATION',
                'ERROR: Translation for MY_MISING_TRANSLATION not found!',
            ],
        ];
    }

    /**
     * @param bool   $isProductiveMode
     * @param string $ident
     * @param string $translation
     *
     * @dataProvider missingTranslationProviderFrontend
     */
    public function testTranslateFrontend_isMissingTranslation($isProductiveMode, $ident, $translation)
    {
        $context = $this->prophesize(ContextInterface::class);
        $context->isShopInProductiveMode()->willReturn($isProductiveMode);
        $multiLangFilterLogic = new TranslateFilterLogic($context->reveal(), $this->getTranslator(1));

        $this->assertEquals($translation, $multiLangFilterLogic->multiLang($ident));
    }

    /**
     * @return ContextInterface
     */
    private function getContextMock()
    {
        return $this->getMockBuilder(ContextInterface::class)->getMock();
    }

    /**
     * @param $languageId
     * @return LegacyTemplateTranslator
     */
    private function getTranslator($languageId)
    {
        $language = Registry::getLang();
        $language->setTplLanguage($languageId);
        $language->setAdminMode(false);
        Registry::set(Language::class, $language);
        return new LegacyTemplateTranslator();
    }
}
