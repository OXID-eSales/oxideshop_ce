<?php

$sMetadataVersion = '1.1';

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
       # \OxidEsales\Eshop\Application\Controller\PaymentController::class => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
       # \OxidEsales\Eshop\Core\Price::class => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice'
       'payment' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
       'oxprice' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice'

    ),
    'files' => array(
        'TestModuleTwoModel'  => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php'
    ),
    'settings' => array(
    )
);
