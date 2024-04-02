<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

trait RequestTrait
{
    private array $post;

    public function backupRequestData(): void
    {
        $this->post = $_POST;
    }

    public function restoreRequestData(): void
    {
        $_POST = $this->post;
    }
}
