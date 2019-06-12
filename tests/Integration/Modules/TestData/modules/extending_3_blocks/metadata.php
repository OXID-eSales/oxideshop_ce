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
    'id'           => 'extending_3_blocks',
    'title'        => 'Test extending only 3 blocks classes',
    'description'  => 'Module testing extending 3 blocks classes',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'blocks' => array(
        array('template' => 'page/checkout/basket.tpl',  'block'=>'basket_btn_next_top',    'file'=>'/views/blocks/page/checkout/myexpresscheckout.tpl'),
        array('template' => 'page/checkout/basket.tpl',  'block'=>'basket_btn_next_bottom', 'file'=>'/views/blocks/page/checkout/myexpresscheckout.tpl'),
        array('template' => 'page/checkout/payment.tpl', 'block'=>'select_payment',         'file'=>'/views/blocks/page/checkout/mypaymentselector.tpl'),
    ),
);
