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
$aModule = [
    'id'          => 'with_extending_blocks',
    'title'       => 'Test extending blocks classes',
    'description' => 'Module testing extending blocks classes',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'blocks'      => [
        ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
        ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
        ['template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'],
    ]
];