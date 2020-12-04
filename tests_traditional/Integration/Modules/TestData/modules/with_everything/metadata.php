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
    'id'          => 'with_everything',
    'title'       => 'Test extending 1 shop class',
    'description' => 'Module testing extending 1 shop class',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => array(
        \OxidEsales\Eshop\Application\Model\Article::class => 'with_everything/myarticle',
        \OxidEsales\Eshop\Application\Model\User::class    => 'with_everything/myuser',
        \OxidEsales\Eshop\Application\Model\Order::class    => 'with_everything/myorder1',
    ),
    'blocks'      => array(
        array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
        array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
    ),
    'events'      => array(
        'onActivate'   => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_everything\Event\MyEvents::onActivate',
        'onDeactivate' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_everything\Event\MyEvents::onDeactivate'
    ),
    'templates'   => array(
        'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
    ),
    'settings'    => array(
        array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
        array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
    ),
);
