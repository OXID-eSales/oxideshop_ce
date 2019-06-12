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
    'id'           => 'extending_3_classes_with_1_extension',
    'title'        => 'Test extending 3 shop classes',
    'description'  => 'Module testing extending 3 shop classes',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => array(
        'oxarticle' => 'extending_3_classes_with_1_extension/mybaseclass',
        'oxorder' => 'extending_3_classes_with_1_extension/mybaseclass',
        'oxuser' => 'extending_3_classes_with_1_extension/mybaseclass',
    )
);
