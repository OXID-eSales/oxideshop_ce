<?php

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Extension;

use OxidEsales\EshopCommunity\Internal\Twig\Extensions\SmartyExtension;

/**
 * Class SmartyExtensionTest
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class SmartyExtensionTest extends AbstractExtensionTest
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->extension = new SmartyExtension();
    }

    /**
     * @param string $template
     * @param string $expected
     * @param array  $variables
     *
     * @dataProvider getStaticCycle
     */
    public function testStaticCycle($template, $expected, array $variables = [])
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    /**
     * @return array
     */
    public function getStaticCycle()
    {
        return [
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values) }}",
                "aba",
                ['values' => ["a", "b"]]
            ],
            [
                "{{ smarty_cycle(values, { name: \"cycleName\" }) }}" .
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values, { name: \"cycleName\" }) }}",
                "aab",
                ['values' => ["a", "b"]]
            ],
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values, { reset: true }) }}" .
                "{{ smarty_cycle(values) }}",
                "aab",
                ['values' => ["a", "b"]]
            ],
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle() }}" .
                "{{ smarty_cycle() }}",
                "aba",
                ['values' => ["a", "b"]]
            ],
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values, { advance: false }) }}" .
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values) }}",
                "abba",
                ['values' => ["a", "b"]]
            ],
            [
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values, { print: false }) }}" .
                "{{ smarty_cycle(values) }}" .
                "{{ smarty_cycle(values) }}",
                "aab",
                ['values' => ["a", "b"]]
            ]
        ];
    }
}
