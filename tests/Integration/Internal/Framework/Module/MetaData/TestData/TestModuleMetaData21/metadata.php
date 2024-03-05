<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Payment;

$sMetadataVersion = '2.1';

$aModule = [
    'id'                      => 'TestModuleMetaData21',
    'title'                   => 'Module for testModuleMetaData21',
    'description'             => [
        'de' => 'de description for testModuleMetaData21',
        'en' => 'en description for testModuleMetaData21',
    ],
    'lang'                    => 'en',
    'thumbnail'               => 'picture.png',
    'version'                 => '1.0',
    'author'                  => 'OXID eSales AG',
    'url'                     => 'https://www.oxid-esales.com',
    'email'                   => 'info@oxid-esales.com',
    'extend'                  => [
        Payment::class => 'TestModuleMetaData21\Payment',
        'oxArticle'                                        => 'TestModuleMetaData21\Article'
    ],
    'controllers'             => [
        'myvendor_mymodule_MyModuleController'      => 'TestModuleMetaData21\Controller',
        'myvendor_mymodule_MyOtherModuleController' => 'TestModuleMetaData21\OtherController',
    ],
    'blocks'                  => [
        [
            'theme'    => 'theme_id',
            'template' => 'template_1.html.twig',
            'block'    => 'block_1',
            'file'     => '/blocks/template_1.html.twig',
            'position' => '1'
        ],
        [
            'template' => 'template_2.html.twig',
            'block'    => 'block_2',
            'file'     => '/blocks/template_2.html.twig',
            'position' => '2'
        ],
    ],
    'settings' => [
        [
            'group' => 'main',
            'name' => 'setting_1',
            'type' => 'select',
            'value' => '0',
            'constraints' => '0|1|2|3',
            'position' => 3
        ],
        ['group' => 'main', 'name' => 'setting_2', 'type' => 'password', 'value' => 'changeMe']
    ],
    'events'                  => [
        'onActivate'   => 'TestModuleMetaData21\Events::onActivate',
        'onDeactivate' => 'TestModuleMetaData21\Events::onDeactivate'
    ],
];
