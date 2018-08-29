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
$aModule = [
    'id'           => 'test11',
    'title'        => 'Test module #11',
    'description'  => 'Test module for oxajax container-class resolution',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'controllers'   => [
        'test_11_ajax_controller_ajax' => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\test11\Application\Controller\Test11AjaxControllerAjax::class,
        'test_11_ajax_controller' => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\test11\Application\Controller\Test11AjaxController::class
    ],
    'templates'   =>  ['test_11_ajax_controller.tpl' => 'oxid/test11/Application/Views/tpl/test_11_ajax_controller.tpl',
                       'test_11_popup.tpl' => 'oxid/test11/Application/Views/tpl/test_11_popup.tpl']
];
