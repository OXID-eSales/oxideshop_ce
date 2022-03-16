<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use Codeception\Actor;
use Codeception\Lib\Actor\Shared\Retry;
use Codeception\Scenario;

class AcceptanceActor extends Actor
{
    use Retry;

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);
        $this->retryNum = 5;
        $this->retryInterval = 350;
    }
}
