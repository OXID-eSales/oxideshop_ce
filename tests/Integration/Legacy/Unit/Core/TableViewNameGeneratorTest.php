<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use PHPUnit\Framework\MockObject\MockObject;

class TableViewNameGeneratorTest extends \PHPUnit\Framework\TestCase
{
    public function testLanguageTableViewNameGenerationWhenDefaultLanguageIsUsed()
    {
        /** @var oxConfig|MockObject $config */
        $config = $this->getConfig();

        /** @var oxLang|MockObject $language */
        $language = $this->getMock(\OxidEsales\Eshop\Core\Language::class, ['getMultiLangTables', 'getBaseLanguage', 'getLanguageAbbr']);
        $language->expects($this->any())->method('getMultiLangTables')->will($this->returnValue(['test_table1', 'test_table2']));
        $language->expects($this->any())->method('getBaseLanguage')->will($this->returnValue('baseLanguage'));
        $language->expects($this->any())->method('getLanguageAbbr')->with('baseLanguage')->will($this->returnValue('te'));

        $viewNameGenerator = oxNew('oxTableViewNameGenerator', $config, $language);
        $this->assertEquals('oxv_test_table1_te', $viewNameGenerator->getViewName('test_table1'));
    }

    public function testLanguageTableViewNameGenerationWhenLanguageIsPassed()
    {
        /** @var oxConfig|MockObject $config */
        $config = $this->getConfig();

        /** @var oxLang|MockObject $language */
        $language = $this->getMock(\OxidEsales\Eshop\Core\Language::class, ['getMultiLangTables', 'getBaseLanguage', 'getLanguageAbbr']);
        $language->expects($this->any())->method('getMultiLangTables')->will($this->returnValue(['test_table1', 'test_table2']));
        $language->expects($this->any())->method('getBaseLanguage')->will($this->returnValue('baseLanguage'));
        $language->expects($this->any())->method('getLanguageAbbr')->with('passedLanguage')->will($this->returnValue('te'));

        $viewNameGenerator = oxNew('oxTableViewNameGenerator', $config, $language);
        $this->assertEquals('oxv_test_table1_te', $viewNameGenerator->getViewName('test_table1', 'passedLanguage'));
    }

    public function testViewNameGenerationWithNonMultiLangAndNonMultiShopTable()
    {
        /** @var oxConfig|MockObject $config */
        $config = $this->getConfig();

        /** @var oxLang|MockObject $language */
        $language = $this->getMock(\OxidEsales\Eshop\Core\Language::class, ['getMultiLangTables', 'getBaseLanguage', 'getLanguageAbbr']);
        $language->expects($this->any())->method('getMultiLangTables')->will($this->returnValue([]));

        $viewNameGenerator = oxNew('oxTableViewNameGenerator', $config, $language);
        $this->assertEquals('non_multi_lang_table', $viewNameGenerator->getViewName('non_multi_lang_table'));
    }

    public function testTableViewNameGenerationWithNegativeLanguage()
    {
        /** @var oxConfig|MockObject $config */
        $config = $this->getConfig();

        /** @var oxLang|MockObject $language */
        $language = $this->getMock(\OxidEsales\Eshop\Core\Language::class, ['getMultiLangTables', 'getBaseLanguage', 'getLanguageAbbr']);
        $language->expects($this->any())->method('getMultiLangTables')->will($this->returnValue(['table1']));

        $viewNameGenerator = oxNew('oxTableViewNameGenerator', $config, $language);
        $this->assertEquals('oxv_table1', $viewNameGenerator->getViewName('table1', -1));
    }
}
