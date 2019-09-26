<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFilterLogic;
use OxidEsales\TestingLibrary\UnitTestCase;

class TranslateLogicTest extends UnitTestCase
{
    /** @var TranslateFilterLogic */
    private $multiLangFilterLogic;

    protected function setUp()
    {
        parent::setUp();
        $this->multiLangFilterLogic = new TranslateFilterLogic();
    }

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
        $this->setLanguage($languageId);
        $this->assertEquals($result, $this->multiLangFilterLogic->multiLang($ident));
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
        $this->setLanguage($languageId);
        $this->assertEquals($result, $this->multiLangFilterLogic->multiLang($ident, $arguments));
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
        $this->setAdminMode(false);
        $this->setLanguage(1);

        $oShop = $this->getConfig()->getActiveShop();
        $oShop->oxshops__oxproductive = new Field($isProductiveMode);
        $oShop->save();

        $this->assertEquals($translation, $this->multiLangFilterLogic->multiLang($ident));
    }

    /**
     * testTranslateAdmin_isMissingTranslation data provider
     *
     * @return array
     */
    public function missingTranslationProviderAdmin(): array
    {
        return [
            [
                'MY_MISING_TRANSLATION',
                'ERROR: Translation for MY_MISING_TRANSLATION not found!',
            ],
        ];
    }

    /**
     * @param string $ident
     * @param string $translation
     *
     * @dataProvider missingTranslationProviderAdmin
     */
    public function testTranslateAdmin_isMissingTranslation($ident, $translation)
    {
        $this->setLanguage(1);
        $this->setAdminMode(true);

        $this->assertEquals($translation, $this->multiLangFilterLogic->multiLang($ident));
    }

}
