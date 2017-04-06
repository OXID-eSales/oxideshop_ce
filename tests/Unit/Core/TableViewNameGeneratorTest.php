<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

class TableViewNameGeneratorTest extends \OxidTestCase
{
    public function testLanguageTableViewNameGenerationWhenDefaultLanguageIsUsed()
    {
        /** @var oxConfig|MockObject $config */
        $config = $this->getConfig();

        /** @var oxLang|MockObject $language */
        $language = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getMultiLangTables', 'getBaseLanguage', 'getLanguageAbbr'));
        $language->expects($this->any())->method('getMultiLangTables')->will($this->returnValue(array('test_table1', 'test_table2')));
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
        $language = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getMultiLangTables', 'getBaseLanguage', 'getLanguageAbbr'));
        $language->expects($this->any())->method('getMultiLangTables')->will($this->returnValue(array('test_table1', 'test_table2')));
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
        $language = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getMultiLangTables', 'getBaseLanguage', 'getLanguageAbbr'));
        $language->expects($this->any())->method('getMultiLangTables')->will($this->returnValue(array()));

        $viewNameGenerator = oxNew('oxTableViewNameGenerator', $config, $language);
        $this->assertEquals('non_multi_lang_table', $viewNameGenerator->getViewName('non_multi_lang_table'));
    }

    public function testTableViewNameGenerationWithNegativeLanguage()
    {
        /** @var oxConfig|MockObject $config */
        $config = $this->getConfig();

        /** @var oxLang|MockObject $language */
        $language = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getMultiLangTables', 'getBaseLanguage', 'getLanguageAbbr'));
        $language->expects($this->any())->method('getMultiLangTables')->will($this->returnValue(array('table1')));

        $viewNameGenerator = oxNew('oxTableViewNameGenerator', $config, $language);
        $this->assertEquals('oxv_table1', $viewNameGenerator->getViewName('table1', -1));
    }
}
