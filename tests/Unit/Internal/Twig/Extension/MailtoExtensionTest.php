<?php

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Extension;

use OxidEsales\EshopCommunity\Internal\Twig\Extensions\MailtoExtension;

class MailtoExtensionTest extends AbstractExtensionTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->extension = new MailtoExtension();
    }

    /**
     * @param $template
     * @param $expected
     * @param array $variables
     *
     * @dataProvider getMailtoTests
     */
    public function testMailto($template, $expected, array $variables = [])
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($variables));
    }

    /**
     * @return array
     */
    public function getMailtoTests()
    {
        return [
            [
                "{{ mailto(\"me@example.com\") }}",
                "<a href=\"mailto:me@example.com\" >me@example.com</a>"
            ],
            [
                "{{ mailto(\"me@example.com\", { text: \"send me some mail\" }) }}",
                "<a href=\"mailto:me@example.com\" >send me some mail</a>"
            ],
            [
                "{{ mailto(\"me@example.com\", { encode: \"javascript\" }) }}",
                "<script type=\"text/javascript\">eval(unescape('%64%6f%63%75%6d%65%6e%74%2e%77%72%69%74%65%28%27%3c%61%20%68%72%65%66%3d%22%6d%61%69%6c%74%6f%3a%6d%65%40%65%78%61%6d%70%6c%65%2e%63%6f%6d%22%20%3e%6d%65%40%65%78%61%6d%70%6c%65%2e%63%6f%6d%3c%2f%61%3e%27%29%3b'))</script>"
            ],
            [
                "{{ mailto(\"me@example.com\", { encode: \"hex\" }) }}",
                "<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%65%78%61%6d%70%6c%65.%63%6f%6d\">&#x6d;&#x65;&#x40;&#x65;&#x78;&#x61;&#x6d;&#x70;&#x6c;&#x65;&#x2e;&#x63;&#x6f;&#x6d;</a>"
            ],
            [
                "{{ mailto(\"me@example.com\", { subject: \"Hello to you!\" }) }}",
                "<a href=\"mailto:me@example.com?subject=Hello%20to%20you%21\" >me@example.com</a>"
            ],
            [
                "{{ mailto(\"me@example.com\", { cc: \"you@example.com,they@example.com\" }) }}",
                "<a href=\"mailto:me@example.com?cc=you@example.com,they@example.com\" >me@example.com</a>"
            ],
            [
                "{{ mailto(\"me@example.com\", { extra: 'class=\"email\"' }) }}",
                "<a href=\"mailto:me@example.com\" class=\"email\">me@example.com</a>"
            ],
            [
                "{{ mailto(\"me@example.com\", { encode: \"javascript_charcode\" }) }}",
                "<script type=\"text/javascript\" language=\"javascript\">\n" .
                "<!--\n" .
                "{document.write(String.fromCharCode(60,97,32,104,114,101,102,61,34,109,97,105,108,116,111,58,109,101,64,101,120,97,109,112,108,101,46,99,111,109,34,32,62,109,101,64,101,120,97,109,112,108,101,46,99,111,109,60,47,97,62))}\n" .
                "//-->\n" .
                "</script>"
            ]
        ];
    }
}
