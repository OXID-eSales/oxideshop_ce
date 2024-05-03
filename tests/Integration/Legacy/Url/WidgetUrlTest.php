<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Url;

use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use oxRegistry;
use PHPUnit\Framework\Attributes\DataProvider;

final class WidgetUrlTest extends IntegrationTestCase
{
    private string $shopUrl = 'http://www.example.com/';

    public static function providerGetWidgetUrlAddParametersIdNeed(): array
    {
        $basicUrl = 'http://www.example.com/' . 'widget.php';
        $urlWithoutParams = $basicUrl . '?lang=0';

        $urlParameters = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];
        $urlWithParams = $basicUrl . '?lang=0&amp;param1=value1&amp;param2=value2';

        $urlLanguageParameters = [
            'lang' => '1',
            'param1' => 'value1',
            'param2' => 'value2',
        ];
        $urlWithLanguageParams = $basicUrl . '?lang=1&amp;param1=value1&amp;param2=value2';

        $urlLeveledParameters = [
            'lang' => '1',
            'param1' => ['value1', 'value2'],
        ];
        $urlWithLeveledParameters = $basicUrl . '?lang=1&amp;param1%5B0%5D=value1&amp;param1%5B1%5D=value2';

        return [[[], $urlWithoutParams],
            [$urlParameters, $urlWithParams],
            [$urlLanguageParameters, $urlWithLanguageParams],
            [$urlLeveledParameters, $urlWithLeveledParameters],
        ];
    }

    /**
     * Testing getShopHomeUrl for widget getter
     *
     * @param array $urlParameters parameters to add to url.
     * @param string $sUrl to check if form url matches expectation.
     */
    #[DataProvider('providerGetWidgetUrlAddParametersIdNeed')]
    public function testGetWidgetUrlWithParameters(array $urlParameters, string $sUrl): void
    {
        oxRegistry::getLang()->setBaseLanguage(0);

        $config = oxNew('oxConfig');
        $config->setConfigParam('sShopURL', $this->shopUrl);
        $config->init();

        $this->assertEquals($sUrl, $config->getWidgetUrl(null, null, $urlParameters));
    }

    public static function providerGetWidgetUrlAddCorrectLanguage(): array
    {
        return [[1], [2]];
    }

    /**
     * Testing getShopHomeUrl for widget getter
     *
     * @param int $iLang Shop basic language.
     */
    #[DataProvider('providerGetWidgetUrlAddCorrectLanguage')]
    public function testGetWidgetUrlAddCorrectLanguage(int $iLang): void
    {
        oxRegistry::getLang()->setBaseLanguage($iLang);

        $config = oxNew('oxConfig');
        $config->setConfigParam('sShopURL', $this->shopUrl);
        $config->init();

        $this->assertEquals($this->shopUrl . 'widget.php?lang=' . $iLang, $config->getWidgetUrl());
    }

    public function providerGetWidgetUrlAddCorrectLanguageWithParameter(): array
    {
        return [[1], [2]];
    }

    /**
     * Testing getShopHomeUrl for widget getter
     *
     * @param int $iLang Shop basic language.
     */
    #[DataProvider('providerGetWidgetUrlAddCorrectLanguage')]
    public function testGetWidgetUrlAddCorrectLanguageWithParameter(int $iLang): void
    {
        oxRegistry::getLang()->setBaseLanguage(1);

        $config = oxNew('oxConfig');
        $config->setConfigParam('sShopURL', $this->shopUrl);
        $config->init();

        $this->assertEquals($this->shopUrl . 'widget.php?lang=' . $iLang, $config->getWidgetUrl($iLang));
    }
}
