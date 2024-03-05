<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

redirectIfShopNotConfigured();

OxidEsales\EshopCommunity\Core\Oxid::run();
