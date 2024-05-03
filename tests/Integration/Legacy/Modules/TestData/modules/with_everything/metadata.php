<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'with_everything',
    'title' => 'Test extending 1 shop class',
    'description' => 'Module testing extending 1 shop class',
    'thumbnail' => 'picture.png',
    'version' => '1.0',
    'author' => 'OXID eSales AG',
    'extend' => [
        Article::class => 'with_everything/myarticle',
        User::class => 'with_everything/myuser',
        Order::class => 'with_everything/myorder1',
    ],
    'blocks' => [[
        'template' => 'page/checkout/basket.tpl',
        'block' => 'basket_btn_next_top',
        'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl',
    ], [
        'template' => 'page/checkout/payment.tpl',
        'block' => 'select_payment',
        'file' => '/views/blocks/page/checkout/mypaymentselector.tpl',
    ]],
    'events' => [
        'onActivate' => 'OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_everything\Event\MyEvents::onActivate',
        'onDeactivate' => 'OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_everything\Event\MyEvents::onDeactivate',
    ],
    'templates' => [
        'order_special.tpl' => 'with_everything/views/admin/tpl/order_special.tpl',
        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
    ],
    'settings' => [[
        'group' => 'my_checkconfirm',
        'name' => 'blCheckConfirm',
        'type' => 'bool',
        'value' => 'true',
    ], [
        'group' => 'my_displayname',
        'name' => 'sDisplayName',
        'type' => 'str',
        'value' => 'Some name',
    ]],
];
