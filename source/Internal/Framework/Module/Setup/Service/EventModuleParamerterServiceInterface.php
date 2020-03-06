<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Iterator\EventModuleParametersInterface;

/**
 * Interface EventModuleParamerterServiceInterface
 * @package OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service
 */
interface EventModuleParamerterServiceInterface
{

    /**
     * EventModuleParamerterServiceInterface constructor.
     *
     * @param EventModuleParametersInterface $forActivate
     * @param EventModuleParametersInterface $forDeactivate
     */
    public function __construct(
        EventModuleParametersInterface $forActivate,
        EventModuleParametersInterface $forDeactivate
    );

    /**
     * @param callable $callable
     */
    public function forActivate(callable $callable);

    /**
     * @param callable $callable
     */
    public function forDeactivate(callable $callable);

}
