<?php

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Extension;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\OxgetseourlLogic;
use OxidEsales\EshopCommunity\Internal\Twig\Extensions\OxidExtension;

class OxidExtensionTest extends AbstractExtensionTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->extension = new OxidExtension(new OxgetseourlLogic(new \Smarty()));
    }

    /**
     * @param $template
     * @param $expected
     * @param array $variables
     *
     * @dataProvider getOxgetseourlTests
     */
    public function testOxgetseourl($template, $expected, array $variables = [])
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    /**
     * @return array
     */
    public function getOxgetseourlTests()
    {
        return [
            [
                "{{ oxgetseourl({ ident: '' }) }}",
                ""
            ],
        ];
    }
}
