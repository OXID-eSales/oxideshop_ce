<?php

$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'without_own_module_namespace',
    'title'        => 'OXID eShop not namespaced test module',
    'description'  => 'Double the price. Show payment error message during checkout.',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'extend'      => array(
       'payment' => \OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\without_own_module_namespace\Application\Controller\TestModuleTwoPaymentController::class,
       'oxprice' => \OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\without_own_module_namespace\Application\Model\TestModuleTwoPrice::class
    ),
    'settings' => array(
    )
);
