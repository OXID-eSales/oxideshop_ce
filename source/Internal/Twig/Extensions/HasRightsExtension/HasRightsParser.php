<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension;

class HasRightsParser extends \Twig_TokenParser
{

    /**
     * @param \Twig_Token $token
     * @return HasRightsNode|\Twig_Node
     * @throws \Twig_Error_Syntax
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $params = array_merge([], $this->getInlineParams());

        $continue = true;
        while($continue) {
            // create subtree until the decideMyTagFork() callback returns true
            $body = $this->parser->subparse([$this, 'decideMyTagFork']);

            $tag = $stream->next()->getValue();
            switch($tag) {
                case 'endhasrights':
                    $continue = false;
                    break;
                default:
                    throw new \Twig_Error_Syntax(sprintf('Unexpected end of template. Twig was looking for the following tags "endhasrights" to close the "hasrights" block started at line %d)', $lineno), -1);
            }

            array_unshift($params, $body);
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        }
        return new HasRightsNode(new \Twig_Node($params), $lineno, $this->getTag());
    }

    /**
     * Recovers all tag parameters until we find a BLOCK_END_TYPE ( %} )
     *
     * @return array
     */
    protected function getInlineParams()
    {
        $stream = $this->parser->getStream();
        $params = array();
        while(!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            $params[] = $this->parser->getExpressionParser()->parseExpression();
        }
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        return $params;
    }

    /**
     * Callback called at each tag name when subparsing, must return
     * true when the expected end tag is reached.
     *
     * @param \Twig_Token $token
     * @return bool
     */
    public function decideMyTagFork(\Twig_Token $token)
    {
        return $token->test(['endhasrights']);
    }

    /**
     * Your tag name: if the parsed tag match the one you put here, your parse()
     * method will be called.
     *
     * @return string
     */
    public function getTag()
    {
        return 'hasrights';
    }

}