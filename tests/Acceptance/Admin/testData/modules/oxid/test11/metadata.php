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
    'id'           => 'test11',
    'title'        => 'Test module #11',
    'description'  => 'Test module for oxajax container-class resolution',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'controllers'   => [
        'test_11_ajax_controller_ajax' => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\test11\Application\Controller\Test11AjaxController::class,
        'test_11_tab_controller' => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\test11\Application\Controller\Test11TabController::class
    ],
    'templates'   =>  ['test_11_tab.tpl' => 'oxid/test11/Application/Views/tpl/test_11_tab.tpl']
);
