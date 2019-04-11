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
    'id'           => 'module_inheritance_2_3_a',
    'title'        => 'Test case 2.3: namespaced module class extends an other modules extended plain module class.',
    'description'  => 'This module is the second mentioned module, the first in the inheritance chain.',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'files'        => array(
        'vendor_1_module_1_myclass2' => 'oeTest/module_inheritance_2_3_a/vendor_1_module_1_myclass.php',
        'vendor_1_module_1_anotherclass' => 'oeTest/module_inheritance_2_3_a/vendor_1_module_1_anotherclass.php'
    )
);
