<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

// This is acceptance bootstrap
$helper = new \OxidEsales\Codeception\Module\FixturesHelper();
$helper->loadRuntimeFixtures(dirname(__FILE__).'/../_data/fixtures.php');
$helper->loadRuntimeFixtures(dirname(__FILE__).'/../_data/product_attributes.php');
$helper->loadRuntimeFixtures(dirname(__FILE__).'/../_data/user_additional_information.php');
$helper->loadRuntimeFixtures(dirname(__FILE__).'/../_data/user.php');
$helper->loadRuntimeFixtures(dirname(__FILE__).'/../_data/product.php');
$helper->loadRuntimeFixtures(dirname(__FILE__).'/../_data/product_description.php');
$helper->loadRuntimeFixtures(dirname(__FILE__).'/../_data/category.php');
