<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'test2',
    'title'        => 'Test module #2',
    'description'  => 'Appends "+ test2 to content title"',
    'thumbnail'    => 'module.png',
    'version'      => '1.0',
    'author'       => 'OXID',
    'extend'      => array(
        'content' => 'test2/view/myinfo2'
    )
);
