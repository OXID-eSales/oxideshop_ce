<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Node;

use OxidEsales\EshopCommunity\Internal\Twig\Extensions\IfContentExtension;
use OxidEsales\EshopCommunity\Internal\Twig\Node\IfContentNode;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\TextNode;
use Twig\Test\NodeTestCase;

/**
 * Class IfContentNodeTest
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class IfContentNodeTest extends NodeTestCase
{

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $body = new TextNode('Lorem Ipsum', 1);
        $variable = new AssignNameExpression('foo', 1);
        $node = new IfContentNode($body, [], $variable, 1);

        $this->assertEquals($body, $node->getNode('body'));
        $this->assertEquals($variable, $node->getNode('variable'));

        $expr = new ConstantExpression("oxsomething", 1);

        $node = new IfContentNode($body, ['ident' => $expr], $variable, 1);
        $this->assertEquals($expr, $node->getNode('ident'));
        $this->assertFalse($node->hasNode('oxid'));

        $node = new IfContentNode($body, ['oxid' => $expr], $variable, 1);
        $this->assertEquals($expr, $node->getNode('oxid'));
        $this->assertFalse($node->hasNode('ident'));
    }

    /**
     * @return array
     */
    public function getTests()
    {
        $ifContentExtensionClass = IfContentExtension::class;

        $tests = [];

        $body = new TextNode('Lorem Ipsum', 1);
        $variable = new AssignNameExpression('foo', 1);
        $expr = new ConstantExpression("oxsomething", 1);
        $node = new IfContentNode($body, ['ident' => $expr], $variable, 1);
        $tests[] = [$node, <<<EOF
// line 1
\$context["foo"] = \$this->extensions['$ifContentExtensionClass']->getContent("oxsomething", null);
echo "Lorem Ipsum";
unset(\$context["foo"]);
EOF
        ];

        $node = new IfContentNode($body, ['oxid' => $expr], $variable, 1);
        $tests[] = [$node, <<<EOF
// line 1
\$context["foo"] = \$this->extensions['$ifContentExtensionClass']->getContent(null, "oxsomething");
echo "Lorem Ipsum";
unset(\$context["foo"]);
EOF
        ];


        return $tests;
    }
}
