<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Extension;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\AddUrlParametersLogic;
use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\SeoUrlLogic;
use OxidEsales\EshopCommunity\Internal\Twig\Extensions\UrlExtension;

/**
 * Class UrlExtensionTest
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class UrlExtensionTest extends AbstractExtensionTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->extension = new UrlExtension(new SeoUrlLogic(), new AddUrlParametersLogic());
    }

    /**
     * @param $template
     * @param $expected
     * @param array $variables
     *
     * @dataProvider getSeoUrlTests
     */
    public function testSeoUrl($template, $expected, array $variables = [])
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    /**
     * @param $template
     * @param $expected
     * @param array $variables
     *
     * @dataProvider getAddUrlParametersTests
     */
    public function testAddUrlParameters($template, $expected, array $variables = [])
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    /**
     * @return array
     */
    public function getSeoUrlTests()
    {
        return [
            [
                "{{ seo_url({ ident: \"server.local?df=ab\", params: \"order=abc\" }) }}",
                "server.local?df=ab&amp;order=abc"
            ],
        ];
    }

    /**
     * @return array
     */
    public function getAddUrlParametersTests()
    {
        return [
            [
                "{{ 'abc'|add_url_parameters('de=fg&hi=&jk=lm') }}",
                "abc?de=fg&amp;hi=&amp;jk=lm"
            ],
        ];
    }
}
