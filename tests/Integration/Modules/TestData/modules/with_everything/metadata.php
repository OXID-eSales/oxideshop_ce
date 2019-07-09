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
    'id'           => 'with_everything',
    'title'        => 'Test extending 1 shop class',
    'description'  => 'Module testing extending 1 shop class',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => array(
        'oxarticle' => 'with_everything/myarticle',
        'oxuser' => 'with_everything/myuser',
        'oxorder' => 'with_everything/myorder1',
    ),
    'blocks' => array(
        array('template' => 'page/checkout/basket.tpl',  'block'=>'basket_btn_next_top',    'file'=>'/views/blocks/page/checkout/myexpresscheckout.tpl'),
        array('template' => 'page/checkout/payment.tpl', 'block'=>'select_payment',         'file'=>'/views/blocks/page/checkout/mypaymentselector.tpl'),
    ),
    'templates' => array(
        'order_special.tpl'      => 'with_everything/views/admin/tpl/order_special.tpl',
        'user_connections.tpl'   => 'with_everything/views/tpl/user_connections.tpl',
    ),
    'files' => array(
        'myexception'  => 'with_everything/core/exception/myexception.php',
        'myconnection' => 'with_everything/core/exception/myconnection.php',
    ),
    'settings' => array(
        array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
        array('group' => 'my_displayname',  'name' => 'sDisplayName',   'type' => 'str',  'value' => 'Some name'),
    ),
);
