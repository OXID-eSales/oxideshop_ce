<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\StateMachine;

/**
 * @internal
 */
interface StateMachineInterface
{
    /**
     * @param string $startStateName
     * @param string $transitionName
     */
    public function transition(string $startStateName, string $transitionName);

    /**
     * @param string $startStateName
     * @return array
     */
    public function getPossibleTransitions(string $startStateName): array;
}
