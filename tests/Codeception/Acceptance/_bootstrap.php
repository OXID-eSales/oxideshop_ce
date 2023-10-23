<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Codeception\Module\FixturesHelper;
use Symfony\Component\Filesystem\Path;

require_once Path::join(dirname(__DIR__, 2), 'bootstrap.php');

$helper = new FixturesHelper();
$helper->loadRuntimeFixtures(codecept_data_dir('user.php'));
$helper->loadRuntimeFixtures(codecept_data_dir('voucher.php'));
$helper->loadRuntimeFixtures(codecept_data_dir('order.php'));
$helper->loadRuntimeFixtures(codecept_data_dir('product.php'));
$helper->loadRuntimeFixtures(codecept_data_dir('shop.php'));
