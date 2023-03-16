<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use Symfony\Component\Filesystem\Path;
require_once Path::join(dirname(__DIR__, 2), 'bootstrap.php');

// This is acceptance bootstrap
$helper = new \OxidEsales\Codeception\Module\FixturesHelper();
$helper->loadRuntimeFixtures(dirname(__FILE__) . '/../_data/fixtures.php');
$helper->loadRuntimeFixtures(dirname(__FILE__) . '/../_data/voucher.php');
