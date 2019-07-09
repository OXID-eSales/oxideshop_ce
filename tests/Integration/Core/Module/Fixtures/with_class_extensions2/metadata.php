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
    'id'          => 'with_class_extensions2',
    'title'       => 'with_class_extensions2 module',
    'description' => 'test module',
    'thumbnail'   => 'module.png',
    'version'     => '1.0',
    'author'      => 'OXID',
    'extend'      => [
        ContentController::class => \OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\with_class_extenstions2\Controllers\ContentController::class
    ],
);
