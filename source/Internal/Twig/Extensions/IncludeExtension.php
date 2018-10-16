<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\IncludeDynamicLogic;
use Twig\Extension\AbstractExtension;

/**
 * Class IncludeExtension
 */
class IncludeExtension extends AbstractExtension
{

    /** @var IncludeDynamicLogic */
    private $includeDynamicLogic;

    /**
     * IncludeExtension constructor.
     *
     * @param IncludeDynamicLogic $includeDynamicLogic
     */
    public function __construct(IncludeDynamicLogic $includeDynamicLogic)
    {
        $this->includeDynamicLogic = $includeDynamicLogic;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function renderForCache(array $parameters)
    {
        return $this->includeDynamicLogic->renderForCache($parameters);
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    public function includeDynamicPrefix(array $parameters)
    {
        return $this->includeDynamicLogic->includeDynamicPrefix($parameters);
    }
}
