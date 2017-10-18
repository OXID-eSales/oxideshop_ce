<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'with_metadata_v2',
    'title'        => 'Test extending 1 shop class',
    'description'  => 'Module testing extending 1 shop class',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => ['oxarticle' => 'with_metadata_v2/myarticle'],
    'templates' => array(
        'order_special.tpl'      => 'with_metadata_v2/views/admin/tpl/order_special.tpl',
        'user_connections.tpl'   => 'with_metadata_v2/views/tpl/user_connections.tpl',
    ),
    'controllers'  => [
        'with_metadata_v2_MyModuleController' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\testData\modules\with_metadata_v2\MyModuleController',
        'with_metadata_v2_MyOtherModuleController' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\testData\modules\with_metadata_v2\MyOtherModuleController'
    ]
);
