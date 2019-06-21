<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'with_2_templates',
    'title'        => 'Test with 2 templates added',
    'description'  => 'Module testing with 2 templates added',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'templates' => array(
        'order_special.tpl'      => 'with_2_templates/views/admin/tpl/order_special.tpl',
        'user_connections.tpl'   => 'with_2_templates/views/tpl/user_connections.tpl',
    ),
);
