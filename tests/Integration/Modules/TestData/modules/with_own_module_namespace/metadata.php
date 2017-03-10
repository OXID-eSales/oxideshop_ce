<?php

/**
 * Module information
 */
$aModule = array(
    'id'           => 'EshopTestModuleOne',
    'title'        => 'OXID eShop namespaced test module',
    'description'  => 'Double the price. Show payment error message during checkout.',
    'thumbnail'    => 'module.png',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'extend'      => array(
        \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopTestModule\Application\Controller\TestModuleOnePaymentController::class,
        \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice::class
    ),
    'files' => array(
    ),
    'settings' => array(
    )
);
