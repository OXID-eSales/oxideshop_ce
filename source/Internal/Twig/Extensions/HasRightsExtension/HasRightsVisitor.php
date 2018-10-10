<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension;

class HasRightsVisitor implements \Twig_NodeVisitorInterface
{

    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return \Twig_Node
     */
    public function enterNode(\Twig_Node $node, \Twig_Environment $env)
    {
        if($node instanceof HasRightsNode) {
            //todo convert EE version of oxhasrights
        }
        return $node;
    }

    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return false|\Twig_Node
     */
    public function leaveNode(\Twig_Node $node, \Twig_Environment $env)
    {
        if($node instanceof HasRightsNode) {
            //todo convert EE version of oxhasrights
        }
        return $node;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }

}