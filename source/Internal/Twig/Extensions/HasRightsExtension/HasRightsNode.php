<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension;

class HasRightsNode extends \Twig_Node
{

    /**
     * HasRightsNode constructor.
     * @param $params
     * @param int $lineno
     * @param null $tag
     */
    public function __construct($params, $lineno = 0, $tag = null)
    {
        parent::__construct(['HasRightsParams' => $params], [], $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $count = count($this->getNode('HasRightsParams'));

        $compiler->addDebugInfo($this);

        for($i = 0; ($i < $count); $i++) {
            if(!($this->getNode('HasRightsParams')->getNode($i) instanceof \Twig_Node_Expression)) {
                $compiler->subcompile($this->getNode('HasRightsParams')->getNode($i));
            }
        }
    }

}