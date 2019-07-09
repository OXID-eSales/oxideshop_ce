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
    'id'           => 'extending_1_class_3_extensions',
    'title'        => 'Test extending 1 shop class with 3 extensions',
    'description'  => 'Module testing extending 1 shop class with 3 extensions',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => array(
        'oxorder' => 'oeTest/extending_1_class_3_extensions/myorder1',
    )
);