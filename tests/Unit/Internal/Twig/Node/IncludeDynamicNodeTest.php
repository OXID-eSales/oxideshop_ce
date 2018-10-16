<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Node;


use OxidEsales\EshopCommunity\Internal\Twig\Extensions\IncludeExtension;
use OxidEsales\EshopCommunity\Internal\Twig\Node\IncludeDynamicNode;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConditionalExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Test\NodeTestCase;

/**
 * Class IncludeDynamicNodeTest
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class IncludeDynamicNodeTest extends NodeTestCase
{

    public function testConstructor()
    {
        $expr = new ConstantExpression('foo.twig', 1);
        $node = new IncludeDynamicNode($expr, null, false, false, 1);

        $this->assertFalse($node->hasNode('variables'));
        $this->assertEquals($expr, $node->getNode('expr'));
        $this->assertFalse($node->getAttribute('only'));

        $vars = new ArrayExpression([new ConstantExpression('foo', 1), new ConstantExpression(true, 1)], 1);
        $node = new IncludeDynamicNode($expr, $vars, true, false, 1);
        $this->assertEquals($vars, $node->getNode('variables'));
        $this->assertTrue($node->getAttribute('only'));
    }

    public function getTests()
    {
        $includeExtensionClass = IncludeExtension::class;

        $tests = [];

        $expr = new ConstantExpression('foo.twig', 1);
        $node = new IncludeDynamicNode($expr, null, false, false, 1);
        $tests[] = [$node, <<<EOF
// line 1
if (!empty(\$context["_render4cache"])) {
    echo \$this->extensions['$includeExtensionClass']->renderForCache([]);
} else {
    \$this->loadTemplate("foo.twig", null, 1)->display(\$context);
}
EOF
        ];

        $expr = new ConditionalExpression(
            new ConstantExpression(true, 1),
            new ConstantExpression('foo', 1),
            new ConstantExpression('foo', 1),
            0
        );
        $node = new IncludeDynamicNode($expr, null, false, false, 1);
        $tests[] = [$node, <<<EOF
// line 1
if (!empty(\$context["_render4cache"])) {
    echo \$this->extensions['$includeExtensionClass']->renderForCache([]);
} else {
    \$this->loadTemplate(((true) ? ("foo") : ("foo")), null, 1)->display(\$context);
}
EOF
        ];

        $expr = new ConstantExpression('foo.twig', 1);
        $vars = new ArrayExpression([new ConstantExpression('foo', 1), new ConstantExpression(true, 1)], 1);
        $node = new IncludeDynamicNode($expr, $vars, false, false, 1);
        $tests[] = [$node, <<<EOF
// line 1
\$parameters = array("foo" => true);
if (!empty(\$context["_render4cache"])) {
    echo \$this->extensions['$includeExtensionClass']->renderForCache(\$parameters);
} else {
    \$parameters = \$this->extensions['$includeExtensionClass']->includeDynamicPrefix(\$parameters);
    \$this->loadTemplate("foo.twig", null, 1)->display(array_merge(\$context, \$parameters));
}
EOF
        ];

        $node = new IncludeDynamicNode($expr, $vars, true, false, 1);
        $tests[] = [$node, <<<EOF
// line 1
\$parameters = array("foo" => true);
if (!empty(\$context["_render4cache"])) {
    echo \$this->extensions['$includeExtensionClass']->renderForCache(\$parameters);
} else {
    \$parameters = \$this->extensions['$includeExtensionClass']->includeDynamicPrefix(\$parameters);
    \$this->loadTemplate("foo.twig", null, 1)->display(\$parameters);
}
EOF
        ];

        $node = new IncludeDynamicNode($expr, $vars, true, true, 1);
        $tests[] = [$node, <<<EOF
// line 1
try {
    \$parameters = array("foo" => true);
    if (!empty(\$context["_render4cache"])) {
        echo \$this->extensions['$includeExtensionClass']->renderForCache(\$parameters);
    } else {
        \$parameters = \$this->extensions['$includeExtensionClass']->includeDynamicPrefix(\$parameters);
        \$this->loadTemplate("foo.twig", null, 1)->display(\$parameters);
    }
} catch (Twig_Error_Loader \$e) {
    // ignore missing template
}
EOF
        ];

        return $tests;
    }
}
