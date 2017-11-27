<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = [
    'id'           => 'test12',
    'title'        => 'Test module #12',
    'description'  => 'Test module for oxajax container-class resolution',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'files'   => [
        'test_12_ajax_controller_ajax' => 'oxid/test12/controllers/test_12_ajax_controller_ajax.php',
        'test_12_ajax_controller' => 'oxid/test12/controllers/test_12_ajax_controller.php'
    ],
    'templates'   =>  ['test_12_ajax_controller.tpl' => 'oxid/test12/views/tpl/test_12_ajax_controller.tpl',
                       'test_12_popup.tpl' => 'oxid/test12/views/tpl/test_12_popup.tpl']
];
