<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 *
 * @author Jędrzej Skoczek & Tomasz Kowalewski
 */

namespace OxidEsales\EshopCommunity\Internal\Twig;

/**
 * Class TwigEngineConfiguration
 * @package OxidEsales\EshopCommunity\Internal\Twig
 */
class TwigEngineConfiguration implements TemplateEngineConfigurationInterface
{
    /**
     * @var TwigContextInterface
     */
    private $context;

    /**
     * TemplateEngineConfiguration constructor.
     *
     * @param TwigContextInterface $context
     */
    public function __construct(TwigContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Return an array of twig parameters to configure.
     *
     * @return array
     */
    public function getParameters()
    {
        return [
            'debug' => $this->context->getIsDebug(),
            'cache' => $this->context->getCacheDir(),
        ];
    }
}