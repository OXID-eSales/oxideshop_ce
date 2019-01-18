<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Url;

use oxRegistry;

class WidgetUrlTest extends \oxUnitTestCase
{
    private $shopUrl = 'http://www.example.com/';

    public function providerGetWidgetUrlAddParametersIdNeed()
    {
        $basicUrl = $this->shopUrl . 'widget.php';
        $urlWithoutParams = $basicUrl . '?lang=0';

        $urlParameters = array('param1' => 'value1', 'param2' => 'value2');
        $urlWithParams = $basicUrl . '?lang=0&amp;param1=value1&amp;param2=value2';

        $urlLanguageParameters = array('lang' => '1', 'param1' => 'value1', 'param2' => 'value2');
        $urlWithLanguageParams = $basicUrl . '?lang=1&amp;param1=value1&amp;param2=value2';

        $urlLeveledParameters = array('lang' => '1', 'param1' => array('value1', 'value2'));
        $urlWithLeveledParameters = $basicUrl . '?lang=1&amp;param1%5B0%5D=value1&amp;param1%5B1%5D=value2';

        return array(
            array(array(), $urlWithoutParams),
            array($urlParameters, $urlWithParams),
            array($urlLanguageParameters, $urlWithLanguageParams),
            array($urlLeveledParameters, $urlWithLeveledParameters),
        );
    }

    /**
     * Testing getShopHomeUrl for widget getter
     *
     * @param array $urlParameters parameters to add to url.
     * @param string $sUrl to check if form url matches expectation.
     *
     * @dataProvider providerGetWidgetUrlAddParametersIdNeed
     */
    public function testGetWidgetUrlWithParameters($urlParameters, $sUrl)
    {
        oxRegistry::getLang()->setBaseLanguage(0);

        $config = oxNew('oxConfig');
        $config->setConfigParam('sShopURL', $this->shopUrl);
        $config->init();

        $this->assertEquals($sUrl, $config->getWidgetUrl(null, null, $urlParameters));
    }

    public function providerGetWidgetUrlAddCorrectLanguage()
    {
        return array(
            array(1),
            array(2),
        );
    }

    /**
     * Testing getShopHomeUrl for widget getter
     *
     * @param int $iLang Shop basic language.
     *
     * @dataProvider providerGetWidgetUrlAddCorrectLanguage
     */
    public function testGetWidgetUrlAddCorrectLanguage($iLang)
    {
        oxRegistry::getLang()->setBaseLanguage($iLang);

        $config = oxNew('oxConfig');
        $config->setConfigParam('sShopURL', $this->shopUrl);
        $config->init();

        $this->assertEquals($this->shopUrl. 'widget.php?lang='. $iLang, $config->getWidgetUrl());
    }

    public function providerGetWidgetUrlAddCorrectLanguageWithParameter()
    {
        return array(
            array(1),
            array(2),
        );
    }

    /**
     * Testing getShopHomeUrl for widget getter
     *
     * @param int $iLang Shop basic language.
     *
     * @dataProvider providerGetWidgetUrlAddCorrectLanguage
     */
    public function testGetWidgetUrlAddCorrectLanguageWithParameter($iLang)
    {
        oxRegistry::getLang()->setBaseLanguage(1);

        $config = oxNew('oxConfig');
        $config->setConfigParam('sShopURL', $this->shopUrl);
        $config->init();

        $this->assertEquals($this->shopUrl. 'widget.php?lang='. $iLang, $config->getWidgetUrl($iLang));
    }
}
