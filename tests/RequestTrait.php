<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

trait RequestTrait
{
    private array $post = [];
    private array $env = [];

    public function backupRequestData(): void
    {
        $this->post = $_POST;
        $this->env = $_ENV;
    }

    public function restoreRequestData(): void
    {
        $_POST = $this->post;
        $_ENV = $this->env;
    }
}
