<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
use OxidEsales\Eshop\Application\Controller\ContentController;

$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'namespace1',
    'title'        => 'Namespaced module #1',
    'description'  => 'Appends "+ namespace1 to content title"',
    'thumbnail'    => 'module.png',
    'version'      => '1.0',
    'author'       => 'OXID',
    'extend'      => [
        ContentController::class => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\namespace1\Controllers\ContentController::class
    ],
);
