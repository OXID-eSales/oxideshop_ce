<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\TokenParser;

use OxidEsales\EshopCommunity\Internal\Twig\Node\IfContentNode;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Node;
use Twig\TokenParser\AbstractTokenParser;
use Twig_Token as Token;

/**
 * Class IfContentNodeParser
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class IfContentTokenParser extends AbstractTokenParser
{

    /**
     * Parses a token and returns a node.
     *
     * @param Token $token
     *
     * @return IfContentNode A Twig_Node instance
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $reference = [];

        if ($stream->nextIf(Token::NAME_TYPE, 'ident')) {
            $reference['ident'] = $this->parser->getExpressionParser()->parseExpression();
        }

        if ($stream->nextIf(Token::NAME_TYPE, 'oxid')) {
            $reference['oxid'] = $this->parser->getExpressionParser()->parseExpression();
        }

        if (empty($reference)) {
            throw new SyntaxError("No Ident nor Oxid provided for ifcontent.");
        }

        if ($stream->nextIf(Token::NAME_TYPE, 'set')) {
            /** @var Node $variable */
            $variable = $this->parser->getExpressionParser()->parseAssignmentExpression();
        } else {
            $variable = new AssignNameExpression('oCont', $lineno);
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideBlockEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new IfContentNode($body, $reference, $variable, $lineno, $this->getTag());
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    public function decideBlockEnd(Token $token)
    {
        return $token->test('endifcontent');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'ifcontent';
    }
}
