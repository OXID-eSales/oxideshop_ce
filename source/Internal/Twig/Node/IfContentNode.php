<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Node;

use OxidEsales\EshopCommunity\Internal\Twig\Extensions\IfContentExtension;
use Twig_Node as Node;
use Twig_Compiler as Compiler;

/**
 * Class IfContentNode
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class IfContentNode extends Node
{

    /**
     * IfContentNode constructor.
     *
     * @param Node   $body
     * @param array  $reference
     * @param Node   $variable
     * @param int    $lineno
     * @param string $tag
     */
    public function __construct(Node $body, array $reference, Node $variable, $lineno, $tag = 'ifcontent')
    {
        $nodes = [
            'body' => $body,
            'variable' => $variable
        ] + $reference;

        parent::__construct($nodes, [], $lineno, $tag);
    }

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->subcompile($this->getNode('variable'), false)
            ->raw(" = ")
            ->raw("\$this->extensions['" . IfContentExtension::class . "']->getContent(")
        ;

        if ($this->hasNode('ident')) {
            $compiler->subcompile($this->getNode('ident'))->raw(', null');
        } elseif ($this->hasNode('oxid')) {
            $compiler->raw('null, ')->subcompile($this->getNode('oxid'));
        }

        $compiler->raw(");\n");

        $compiler
            ->subcompile($this->getNode('body'))
            ->write("unset(")->subcompile($this->getNode('variable'))->raw(");\n");
        ;
    }
}
