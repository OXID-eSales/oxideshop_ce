<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions\HasRightsExtension;

use Twig\Extension\AbstractExtension;

class HasRightsExtension extends AbstractExtension
{
    /**
     * @return array|\Twig_TokenParserInterface[]
     */
    public function getTokenParsers()
    {
        return [new HasRightsParser()];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'hasrights';
    }

    /**
     * @return array|\Twig_NodeVisitorInterface[]
     */
    public function getNodeVisitors()
    {
        return [new HasRightsVisitor()];
    }
}