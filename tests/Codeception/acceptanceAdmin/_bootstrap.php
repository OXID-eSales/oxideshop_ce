<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Codeception\Module\FixturesHelper;

$helper = new FixturesHelper();
$helper->loadRuntimeFixtures(__DIR__ . '/../_data/fixtures.php');
