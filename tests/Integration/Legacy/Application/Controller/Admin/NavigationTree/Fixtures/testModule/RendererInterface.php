<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Controller\Admin\Fixtures\testModule;

interface RendererInterface
{
    public function formFilesOutput(): string;
}
