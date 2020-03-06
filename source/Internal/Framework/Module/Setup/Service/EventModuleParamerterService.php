<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Iterator\EventModuleParametersInterface;

/**
 * Class EventModuleParamerterService
 * @package OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service
 */
class EventModuleParamerterService implements EventModuleParamerterServiceInterface
{
    /**
     * @var EventModuleParametersInterface
     */
    private $onActivate = null;

    /**
     * @var EventModuleParametersInterface
     */
    private $onDeactivate = null;

    /**
     * EventModuleParamerterService constructor.
     *
     * @param EventModuleParametersInterface $forActivate
     * @param EventModuleParametersInterface $forDeactivate
     */
    public function __construct(
        EventModuleParametersInterface $forActivate,
        EventModuleParametersInterface $forDeactivate
    ) {
        $this->onActivate = $forActivate;
        $this->onDeactivate  = $forDeactivate;
    }

    /**
     * Create function paramerter for event on Activate
     *
     * @param callable $callable
     */
    public function forActivate(callable $callable)
    {
        $parameters = $this->onActivate->getParameters();

        call_user_func($callable, $parameters);
    }

    /**
     * Create function paramerter for event on Deactivate
     *
     * @param callable $callable
     */
    public function forDeactivate(callable $callable)
    {
        $parameters = $this->onDeactivate->getParameters();

        call_user_func($callable, $parameters);
    }
}
