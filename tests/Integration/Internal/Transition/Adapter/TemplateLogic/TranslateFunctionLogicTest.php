<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Language;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFunctionLogic;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator\LegacyTemplateTranslator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class TranslateFunctionLogicTest extends IntegrationTestCase
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
            ['VAT', 1, 'VAT'],
            [null, 1, 'ERROR: Translation for IDENT MISSING not found!']
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
        $translateLogic = new TranslateFunctionLogic($this->getContextMock(), $this->getTranslator($languageId));
        $params['ident'] = $ident;

        $this->assertEquals($result, $translateLogic->getTranslation($params));
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
        $translateLogic = new TranslateFunctionLogic($this->getContextMock(), $this->getTranslator($languageId));
        $params['ident'] = $ident;
        $params['args'] = $arguments;

        $this->assertEquals($result, $translateLogic->getTranslation($params));
    }

    /**
     * Provides data to testAssignmentsWithSuffix
     *
     * @return array
     */
    public function withSuffixProvider(): array
    {
        return [
            ['FIRST_NAME', 0, 'LAST_NAME', 'VornameNachname'],
            ['FIRST_NAME', 0, 'NO_SUFFIX', 'Vorname'],
        ];
    }

    /**
     * Tests value assignments when translating strings containing %s
     *
     * @param string $ident
     * @param int    $languageId
     * @param mixed  $suffix
     * @param string $result
     *
     * @dataProvider withSuffixProvider
     */
    public function testAssignmentsWithSuffix($ident, $languageId, $suffix, $result)
    {
        $translateLogic = new TranslateFunctionLogic($this->getContextMock(), $this->getTranslator($languageId));
        $params['ident'] = $ident;
        $params['suffix'] = $suffix;

        $this->assertEquals($result, $translateLogic->getTranslation($params));
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
                'MY_MISSING_TRANSLATION',
                'MY_MISSING_TRANSLATION',
            ],
            [
                false,
                'ident' => 'MY_MISSING_TRANSLATION',
                'ERROR: Translation for MY_MISSING_TRANSLATION not found!',
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
        $context->isAdmin()->willReturn(false);
        $translateLogic = new TranslateFunctionLogic($context->reveal(), $this->getTranslator(1));
        $params['ident'] = $ident;

        $this->assertEquals($translation, $translateLogic->getTranslation($params));
    }

    public function testAlternativeTranslation()
    {
        $translateLogic = new TranslateFunctionLogic($this->getContextMock(), $this->getTranslator(1));
        $params['ident'] = 'MY_MISSING_TRANSLATION';
        $params['alternative'] = 'Alternative translation';

        $this->assertEquals('Alternative translation', $translateLogic->getTranslation($params));
    }

    public function testNotExistingTranslationWithoutError()
    {
        $translateLogic = new TranslateFunctionLogic($this->getContextMock(), $this->getTranslator(1));
        $params['ident'] = 'MY_MISSING_TRANSLATION';
        $params['noerror'] = true;

        $this->assertEquals('MY_MISSING_TRANSLATION', $translateLogic->getTranslation($params));
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
