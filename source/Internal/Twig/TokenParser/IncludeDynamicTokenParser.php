<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\TokenParser;

use OxidEsales\EshopCommunity\Internal\Twig\Node\IncludeDynamicNode;
use Twig\TokenParser\IncludeTokenParser;

/**
 * Class IncludeDynamicTokenParser
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class IncludeDynamicTokenParser extends IncludeTokenParser
{

    /**
     * @param \Twig_Token $token
     *
     * @return IncludeDynamicNode|\Twig_Node|\Twig_Node_Include
     */
    public function parse(\Twig_Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        return new IncludeDynamicNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'include_dynamic';
    }
}
