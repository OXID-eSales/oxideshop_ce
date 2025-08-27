<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Codeception\Module\FixturesHelper;

$helper = new FixturesHelper();
$helper->loadRuntimeFixtures(codecept_data_dir('user.php'));
$helper->loadRuntimeFixtures(codecept_data_dir('voucher.php'));
$helper->loadRuntimeFixtures(codecept_data_dir('order.php'));
$helper->loadRuntimeFixtures(codecept_data_dir('product.php'));
$helper->loadRuntimeFixtures(codecept_data_dir('shop.php'));
$helper->loadRuntimeFixtures(codecept_data_dir('category.php'));

date_default_timezone_set(getenv('OXID_DEFAULT_TIMEZONE') ?: 'Europe/Berlin');
