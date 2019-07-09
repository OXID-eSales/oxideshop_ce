<?php

$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'with_own_module_namespace',
    'title'        => 'OXID eShop namespaced test module',
    'description'  => 'Double the price. Show payment error message during checkout.',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'extend'      => array(
        \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController::class,
        \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice::class
    ),
    'files' => array(
    ),
    'settings' => array(
    )
);
