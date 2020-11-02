<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class Event
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $method;

    public function __construct(string $action, string $method)
    {
        $this->action = $action;
        $this->method = $method;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
